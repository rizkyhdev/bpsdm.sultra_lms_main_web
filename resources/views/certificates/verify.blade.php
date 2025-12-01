<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Certificate Verification') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .verification-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        
        .verification-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .verification-status {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        
        .status-valid {
            color: #10b981;
        }
        
        .status-invalid {
            color: #ef4444;
        }
        
        .certificate-details {
            text-align: left;
            background: #f9fafb;
            border-radius: 8px;
            padding: 24px;
            margin-top: 30px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }
        
        .detail-value {
            color: #111827;
            font-weight: 500;
        }
        
        .certificate-uid {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #6b7280;
            margin-top: 20px;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 6px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        @if($status === 'valid' && $certificate)
            <div class="verification-icon">✅</div>
            <div class="verification-status status-valid">
                {{ __('Valid Certificate') }}
            </div>
            
            <div class="certificate-details">
                <div class="detail-row">
                    <span class="detail-label">{{ __('Student Name') }}:</span>
                    <span class="detail-value">{{ $student_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Course Title') }}:</span>
                    <span class="detail-value">{{ $course_title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Completion Date') }}:</span>
                    <span class="detail-value">{{ $completion_date }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Status') }}:</span>
                    <span class="detail-value status-valid">{{ __('Valid') }}</span>
                </div>
            </div>
            
            <div class="certificate-uid">
                <strong>{{ __('Certificate ID') }}:</strong><br>
                {{ $certificate->certificate_uid }}
            </div>
        @else
            <div class="verification-icon">❌</div>
            <div class="verification-status status-invalid">
                {{ __('Certificate Not Found') }}
            </div>
            <p style="color: #6b7280; margin-top: 20px;">
                {{ __('The certificate with the provided ID could not be found. Please verify the certificate ID and try again.') }}
            </p>
        @endif
    </div>
</body>
</html>

