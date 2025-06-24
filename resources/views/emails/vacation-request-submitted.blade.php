<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation Request Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
        }
        .status-badge {
            background-color: #ffc107;
            color: #212529;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
        }
        .next-steps {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏖️ Vacation Request Submitted</h1>
            <p>Your vacation request has been successfully submitted!</p>
        </div>

        <div class="status-badge">
            Status: {{ strtoupper($vacation->status) }}
        </div>

        <h3>Request Details:</h3>
        
        <div class="info-row">
            <span class="info-label">Employee Name:</span>
            <span>{{ $vacation->applicant_name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Vacation Type:</span>
            <span>{{ $vacation->vacationType->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Start Date:</span>
            <span>{{ $vacation->start_date->format('F j, Y') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">End Date:</span>
            <span>{{ $vacation->end_date->format('F j, Y') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Duration:</span>
            <span>{{ $vacation->duration_in_days }} day(s)</span>
        </div>
        
        @if($vacation->reason)
        <div class="info-row">
            <span class="info-label">Reason:</span>
            <span>{{ $vacation->reason }}</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="info-label">Submitted on:</span>
            <span>{{ $vacation->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>

        <div class="next-steps">
            <h4>What happens next?</h4>
            <ul>
                <li>Your request is now in the system with <strong>PENDING</strong> status</li>
                <li>The admin team has been notified and will review your request</li>
                <li>You will receive another email once your request is approved or requires changes</li>
                <li>Please contact HR if you have any questions about your request</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>Important:</strong> This is an automated email. Please do not reply to this message.</p>
            <p>For questions, contact HR at hr@yourcompany.com</p>
            <hr>
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>