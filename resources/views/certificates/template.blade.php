<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Certificate') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5in;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            width: 100%;
            height: 100%;
            position: relative;
            background: #ffffff;
        }
        
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .certificate-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: 0;
        }
        
        .certificate-content {
            position: relative;
            z-index: 1;
            width: 100%;
            text-align: center;
        }
        
        .certificate-border {
            border: 8px solid #2c3e50;
            padding: 60px;
            position: relative;
            background: #ffffff;
        }
        
        .certificate-header {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        
        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #1a237e;
            margin: 40px 0;
            line-height: 1.2;
        }
        
        .certificate-text {
            font-size: 24px;
            color: #333;
            margin: 20px 0;
            line-height: 1.6;
        }
        
        .certificate-student-name {
            font-size: 40px;
            font-weight: bold;
            color: #1a237e;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-thickness: 2px;
        }
        
        .certificate-details {
            font-size: 20px;
            color: #555;
            margin: 25px 0;
        }
        
        .certificate-uid {
            font-size: 14px;
            color: #666;
            margin-top: 40px;
            font-family: 'Courier New', monospace;
        }
        
        .certificate-footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }
        
        .signature-block {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin: 60px auto 10px;
            width: 150px;
        }
        
        .signature-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .signature-title {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .certificate-date {
            font-size: 18px;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        @if($background_image)
        <img src="{{ $background_image }}" alt="" class="certificate-background">
        @endif
        
        <div class="certificate-content">
            <div class="certificate-border">
                <div class="certificate-header">
                    {{ __('Certificate of Completion') }}
                </div>
                
                <div class="certificate-text">
                    {{ __('This is to certify that') }}
                </div>
                
                <div class="certificate-student-name">
                    {{ $student_name }}
                </div>
                
                <div class="certificate-text">
                    {{ __('has successfully completed the course') }}
                </div>
                
                <div class="certificate-title">
                    {{ $course_title }}
                </div>
                
                <div class="certificate-details">
                    {{ __('Issued by') }}: {{ $issuer_name }}
                </div>
                
                <div class="certificate-date">
                    {{ __('Date of Completion') }}: {{ $completion_date }}
                </div>
                
                <div class="certificate-footer">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $instructor_name ?: __('Instructor') }}</div>
                        <div class="signature-title">{{ __('Course Instructor') }}</div>
                    </div>
                    
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $issuer_name }}</div>
                        <div class="signature-title">{{ __('Issuing Authority') }}</div>
                    </div>
                </div>
                
                <div class="certificate-uid">
                    {{ __('Certificate ID') }}: {{ $certificate_uid }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

