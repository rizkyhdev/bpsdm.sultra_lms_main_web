# PDF Certificates Feature

## Overview

The PDF Certificates feature allows authenticated students to download a PDF certificate once their course completion reaches 100%. The system stores generated certificates and allows re-downloading without regenerating the PDF each time.

## Installation

### 1. Install Required Package

```bash
composer require barryvdh/laravel-dompdf
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config
```

### 3. Run Migrations

```bash
php artisan migrate
```

This will add the following fields:
- `slug` to `courses` table
- `completion_percent` to `user_enrollments` table
- `certificate_uid` and `generated_at` to `certificates` table

### 4. Seed Demo Data (Optional)

```bash
php artisan db:seed --class=CertificateDemoSeeder
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Certificate background image (optional)
CERT_BG=/path/to/background-image.png

# Certificate issuer name (defaults to app.name)
CERT_ISSUER_NAME="BPSDM Provinsi Sulawesi Tenggara"

# Download URL TTL in minutes (default: 30)
CERT_DOWNLOAD_TTL=30

# Storage disk for certificates (default: local)
CERT_STORAGE_DISK=local
```

### Config File

The configuration file is located at `config/certificates.php`. You can customize:
- Background image path
- Issuer name
- Download URL TTL
- Storage disk

## Usage

### For Students

#### Download Certificate

Once a student completes a course (100% completion), they can download their certificate:

1. **Via API/JSON Response:**
   ```javascript
   // POST to /courses/{course-slug}/certificate/generate
   fetch('/courses/my-course/certificate/generate', {
       method: 'POST',
       headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
           'Content-Type': 'application/json',
       },
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           window.location.href = data.download_url;
       } else {
           alert(data.message);
       }
   });
   ```

2. **Via Direct Link (with signed URL):**
   ```php
   // In your Blade view
   <a href="{{ route('certificates.download', $course->slug) }}" 
      class="btn btn-primary">
       Download Certificate
   </a>
   ```

#### Frontend Integration Example

Add a "Get Certificate" button in your course detail page:

```blade
@auth
    @php
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->first();
    @endphp
    
    @if($enrollment && $enrollment->completion_percent == 100 && $enrollment->completed_at)
        <button id="get-certificate-btn" class="btn btn-success">
            {{ __('Get Certificate') }}
        </button>
        
        <script>
            document.getElementById('get-certificate-btn').addEventListener('click', function() {
                fetch('{{ route("certificates.generate", $course->slug) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.download_url;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to generate certificate. Please try again.');
                });
            });
        </script>
    @else
        <p class="text-muted">
            {{ __('Complete the course to receive your certificate.') }}
        </p>
    @endif
@endauth
```

### For Administrators

#### Preview Certificate Template

Admins and instructors can preview the certificate template:

```
GET /admin/courses/{course-slug}/certificate/preview
```

#### Generate Certificates via Command

Generate certificates for all eligible users in a course:

```bash
php artisan certificates:generate {course-slug}
```

Generate for a specific user:

```bash
php artisan certificates:generate {course-slug} --user={user-id}
# or
php artisan certificates:generate {course-slug} --user={user-email}
```

### Certificate Verification

Anyone can verify a certificate using its UID:

```
GET /certificates/verify/{uid}
```

This will display:
- Student name
- Course title
- Completion date
- Status (Valid/Not Found)

## Routes

| Method | Route | Description | Middleware |
|--------|-------|-------------|------------|
| POST | `/courses/{course:slug}/certificate/generate` | Generate certificate | `auth` |
| GET | `/courses/{course:slug}/certificate` | Download certificate | `auth`, `signed` |
| GET | `/certificates/verify/{uid}` | Verify certificate | Public |
| GET | `/admin/courses/{course:slug}/certificate/preview` | Preview template | `auth`, `can:preview,course` |

## Business Rules

1. **Eligibility:** A student qualifies for a certificate when:
   - `enrollments.completion_percent == 100`
   - `enrollments.completed_at` is not null
   - User is enrolled in the course

2. **Certificate Generation:**
   - If a certificate doesn't exist, it will be generated and persisted
   - If it exists, the existing file is returned (no regeneration)

3. **Certificate UID:**
   - Each certificate has a unique UUIDv4 (`certificate_uid`)
   - This UID is printed on the PDF
   - Used for verification URL

4. **Storage:**
   - Certificates are stored in: `storage/app/certificates/<year>/<course_slug>/<user_id>.pdf`
   - Never exposed via public storage
   - Always accessed via temporary signed routes

## Security

1. **Authorization:**
   - Only the certificate owner can download their certificate
   - Policy: `CoursePolicy@downloadCertificate`
   - Validates enrollment and completion from database

2. **Signed URLs:**
   - Download URLs are signed and valid for 30 minutes (configurable)
   - Invalid signatures return 403

3. **Access Control:**
   - Preview is restricted to instructors and admins
   - Policy: `CoursePolicy@preview`

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── CertificateController.php
├── Services/
│   └── CertificateService.php
├── Policies/
│   └── CoursePolicy.php (updated)
└── Console/
    └── Commands/
        └── GenerateCertificates.php

resources/
└── views/
    └── certificates/
        ├── template.blade.php
        ├── verify.blade.php
        └── components/
            └── signature.blade.php

config/
└── certificates.php

database/
├── migrations/
│   ├── *_add_slug_to_courses_table.php
│   ├── *_add_completion_percent_to_user_enrollments_table.php
│   └── *_add_certificate_fields_to_certificates_table.php
├── factories/
│   ├── CourseFactory.php
│   ├── UserEnrollmentFactory.php
│   └── CertificateFactory.php
└── seeds/
    └── CertificateDemoSeeder.php

tests/
└── Feature/
    └── CertificateTest.php
```

## Testing

Run the test suite:

```bash
php artisan test
```

Or run specific certificate tests:

```bash
php artisan test --filter CertificateTest
```

## Internationalization

Certificate text is translatable. Add translations to your language files:

```php
// resources/lang/en/certificates.php
return [
    'Certificate of Completion' => 'Certificate of Completion',
    'This is to certify that' => 'This is to certify that',
    'has successfully completed the course' => 'has successfully completed the course',
    'Issued by' => 'Issued by',
    'Date of Completion' => 'Date of Completion',
    'Course Instructor' => 'Course Instructor',
    'Issuing Authority' => 'Issuing Authority',
    'Certificate ID' => 'Certificate ID',
];
```

## Troubleshooting

### Certificate not generating

1. Check that `completion_percent` is exactly 100
2. Verify `completed_at` is not null
3. Ensure user is enrolled in the course
4. Check storage permissions for `storage/app/certificates/`

### PDF generation errors

1. Ensure `barryvdh/laravel-dompdf` is installed
2. Check that fonts are available (DejaVu Sans is included)
3. Verify background image path if configured
4. Check PHP memory limits

### Signed URL issues

1. Ensure `APP_KEY` is set in `.env`
2. Check that URLs are generated within the TTL period
3. Verify route names match exactly

## Changelog

### Version 1.0.0 (Initial Release)

- Added PDF certificate generation for completed courses
- Implemented certificate storage with UUID-based verification
- Added signed URL downloads for security
- Created admin preview functionality
- Added artisan command for bulk certificate generation
- Implemented comprehensive test suite
- Added internationalization support

## Support

For issues or questions, please contact the development team or create an issue in the project repository.

