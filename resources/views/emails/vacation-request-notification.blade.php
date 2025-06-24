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
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }
        .btn-approve {
            background-color: #28a745;
        }
        .btn-reject {
            background-color: #dc3545;
        }
        .btn-view {
            background-color: #007bff;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
        }
        .employee-details {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
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

        <div class="action-buttons">
            <a href="{{ url('/admin/vacations/' . $vacation->id . '/approve') }}" class="btn btn-approve">
                ✅ Approve Request
            </a>
            <a href="{{ url('/admin/vacations/' . $vacation->id) }}" class="btn btn-view">
                👁️ View Details
            </a>
            <a href="{{ url('/admin/vacations/' . $vacation->id . '/reject') }}" class="btn btn-reject">
                ❌ Reject Request
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
            <hr>
            <p>HR Management System - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>