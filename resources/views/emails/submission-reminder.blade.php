<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 30px 15px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #012970 0%, #014C8F 100%);
            padding: 28px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #d0e1fd;
        }
        .gold-bar {
            height: 4px;
            background-color: #FACD06;
            width: 100%;
        }
        .email-body {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #012970;
            margin-bottom: 16px;
        }
        .intro-text {
            font-size: 14px;
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 24px;
        }
        .info-card {
            background-color: #f8fafc;
            border-left: 4px solid #dc3545;
            border-radius: 4px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border-top: 1px solid #edf2f7;
            border-right: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
        }
        .info-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-card td {
            padding: 5px 0;
            font-size: 13px;
            vertical-align: top;
        }
        .info-card .label {
            color: #718096;
            width: 140px;
            font-weight: 500;
        }
        .info-card .value {
            color: #1a202c;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            background-color: #fde8e8;
            color: #9b1c1c;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #f8b4b4;
        }
        .custom-note {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
            padding: 14px 18px;
            margin-bottom: 24px;
            border-top: 1px solid #fef3c7;
            border-right: 1px solid #fef3c7;
            border-bottom: 1px solid #fef3c7;
        }
        .custom-note-title {
            font-size: 13px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 6px;
        }
        .custom-note-body {
            font-size: 13px;
            color: #78350f;
            line-height: 1.5;
            white-space: pre-line;
        }
        .action-container {
            text-align: center;
            margin: 32px 0 20px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #012970;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(1, 41, 112, 0.25);
        }
        .help-text {
            font-size: 12px;
            color: #a0aec0;
            line-height: 1.5;
            margin-top: 24px;
            border-top: 1px solid #edf2f7;
            padding-top: 16px;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #edf2f7;
            font-size: 11px;
            color: #718096;
            line-height: 1.5;
        }
        .email-footer a {
            color: #014C8F;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Top Header -->
            <div class="email-header">
                <h1>GAD Database System</h1>
                <p>Gender and Development Document Management Portal</p>
            </div>
            <div class="gold-bar"></div>

            <!-- Body Content -->
            <div class="email-body">
                <div class="greeting">Hello {{ $user->name }},</div>

                <p class="intro-text">
                    This is an automated reminder regarding your required Gender and Development (GAD) document submission. According to our records, you have <strong>not yet submitted</strong> any document for the specified reporting period.
                </p>

                <!-- Information Summary Card -->
                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">User ID:</td>
                            <td class="value">{{ $user->user_id }}</td>
                        </tr>
                        <tr>
                            <td class="label">Department / Office:</td>
                            <td class="value">{{ $user->department->department_name ?? $user->office ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Reporting Period:</td>
                            <td class="value">{{ $periodLabel }}</td>
                        </tr>
                        <tr>
                            <td class="label">Submission Status:</td>
                            <td class="value">
                                <span class="status-badge">NOT YET SUBMITTED</span>
                            </td>
                        </tr>
                    </table>
                </div>

                @if(!empty($customMessage))
                <!-- Custom Remarks from Administrator -->
                <div class="custom-note">
                    <div class="custom-note-title">Note from Administrator:</div>
                    <div class="custom-note-body">{{ $customMessage }}</div>
                </div>
                @endif

                <p class="intro-text" style="margin-bottom: 8px;">
                    Please log in to the GAD Portal and upload your document at your earliest convenience to complete your submission requirement.
                </p>

                <!-- Call to Action Button -->
                <div class="action-container">
                    <a href="{{ $actionUrl }}" class="action-button" target="_blank">
                        Log In & Submit Document &rarr;
                    </a>
                </div>

                <div class="help-text">
                    <p style="margin: 0 0 6px 0;">If you have already submitted your document or believe this notification was sent in error, please contact your GAD Focal Person or System Administrator.</p>
                    <p style="margin: 0;">Direct URL: <a href="{{ $actionUrl }}" style="color: #014C8F;">{{ $actionUrl }}</a></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 4px 0; font-weight: 600; color: #4a5568;">City Government of Cagayan de Oro</p>
                <p style="margin: 0 0 4px 0;">City Management Information Systems and Innovation Department (CMISID)</p>
                <p style="margin: 0;">Need assistance? Visit the <a href="https://services.cagayandeoro.gov.ph/helpdesk/" target="_blank">CMISID Helpdesk</a></p>
            </div>
        </div>
    </div>
</body>
</html>
