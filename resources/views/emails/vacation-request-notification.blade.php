<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Vacation Request</title>
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
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
        }
        .urgent-badge {
            background-color: #dc3545;
            color: white;
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
            color: #dc3545;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            color: white;
            font-size: 16px;
        }
        .btn-approve {
            background-color: #28a745;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
        .btn-reject {
            background-color: #dc3545;
        }
        .btn-reject:hover {
            background-color: #c82333;
        }
        .btn-view {
            background-color: #007bff;
            display: block;
            margin: 20px auto;
            max-width: 250px;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .employee-details {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .quick-action-note {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🚨 New Vacation Request</h1>
            <p>A new vacation request requires your attention</p>
        </div>

        <div class="urgent-badge">
            ACTION REQUIRED
        </div>

        <div class="employee-details">
            <h4>Employee Information:</h4>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span>{{ $vacation->applicant_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $vacation->applicant_email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span>{{ $vacation->applicant_phone }}</span>
            </div>
        </div>

        <h3>Vacation Request Details:</h3>
        
        <div class="info-row">
            <span class="info-label">Vacation Type:</span>
            <span>{{ $vacation->vacationType->name }} 
                @if($vacation->vacationType->is_paid) 
                    (Paid) 
                @else 
                    (Unpaid) 
                @endif
            </span>
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
            <span><strong>{{ $vacation->duration_in_days }} day(s)</strong></span>
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

        <div class="quick-action-note">
            <strong>⚡ Quick Actions Available!</strong><br>
            You can approve or reject this request directly from this email with one click.
        </div>

        <div class="action-buttons">
            <h3 style="margin-top: 0;">Take Action:</h3>
            <a href="{{ $approveUrl }}" class="btn btn-approve">
                ✅ Approve Request
            </a>
            <a href="{{ $rejectUrl }}" class="btn btn-reject">
                ❌ Reject Request
            </a>
            
            <p style="margin: 20px 0; font-size: 14px; color: #666;">
                <strong>Note:</strong> These links are one-time use and will expire in 7 days.
            </p>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <p><strong>OR</strong></p>
            <a href="{{ $viewUrl }}" class="btn btn-view">
                👁️ View in Admin Dashboard
            </a>
        </div>

        <div style="background-color: #e8f4fd; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4>⚠️ Quick Review Checklist:</h4>
            <ul>
                <li>Check employee's remaining vacation balance</li>
                <li>Verify dates don't conflict with important projects</li>
                <li>Ensure adequate staffing coverage during requested period</li>
                <li>Review company vacation policy compliance</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>This request is currently: PENDING</strong></p>
            <p>Please review and take action as soon as possible.</p>
            <hr style="margin: 15px 0;">
            <p>This is an automated email from the HR Management System.</p>
            <p>&copy; {{ date('Y') }} HRMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
