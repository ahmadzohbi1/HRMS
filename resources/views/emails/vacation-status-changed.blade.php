<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation Request Status Update</title>
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
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header.approved {
            border-bottom: 3px solid #28a745;
        }
        .header.rejected {
            border-bottom: 3px solid #dc3545;
        }
        .header.pending {
            border-bottom: 3px solid #ffc107;
        }
        .header h1 {
            margin: 0;
        }
        .header.approved h1 {
            color: #28a745;
        }
        .header.rejected h1 {
            color: #dc3545;
        }
        .header.pending h1 {
            color: #ffc107;
        }
        .status-badge {
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 15px 0;
            font-size: 16px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .admin-notes {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #2196f3;
        }
        .admin-notes h4 {
            margin-top: 0;
            color: #1976d2;
        }
        .next-steps {
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .next-steps.approved {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        .next-steps.rejected {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .next-steps h4 {
            margin-top: 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .contact-info {
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header {{ $vacation->status }}">
            @if($vacation->status === 'approved')
                <div class="icon">✅</div>
                <h1>Vacation Request Approved!</h1>
                <p>Great news! Your vacation request has been approved.</p>
            @elseif($vacation->status === 'rejected')
                <div class="icon">❌</div>
                <h1>Vacation Request Not Approved</h1>
                <p>We regret to inform you that your vacation request could not be approved at this time.</p>
            @else
                <div class="icon">⏳</div>
                <h1>Vacation Request Status Updated</h1>
                <p>Your vacation request status has been updated.</p>
            @endif
        </div>

        <div class="status-badge status-{{ $vacation->status }}">
            Status: {{ strtoupper($vacation->status) }}
        </div>

        <h3>Request Details:</h3>
        
        <div class="info-row">
            <span class="info-label">Employee Name:</span>
            <span>{{ $vacation->applicant_name }}</span>
        </div>
        
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
            <span class="info-label">Your Reason:</span>
            <span>{{ $vacation->reason }}</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="info-label">Decision Date:</span>
            <span>{{ $vacation->updated_at->format('F j, Y \a\t g:i A') }}</span>
        </div>

        @if($adminNotes)
        <div class="admin-notes">
            <h4>💬 Message from HR:</h4>
            <p>{{ $adminNotes }}</p>
        </div>
        @endif

        @if($vacation->status === 'approved')
        <div class="next-steps approved">
            <h4>🎉 What's Next?</h4>
            <ul>
                <li><strong>Your vacation is confirmed!</strong> Mark your calendar.</li>
                <li>Ensure your work responsibilities are covered during your absence.</li>
                <li>Complete any handover documentation if required.</li>
                <li>Coordinate with your team about ongoing projects.</li>
                <li>Set up an out-of-office message for your email.</li>
                <li>Enjoy your well-deserved time off!</li>
            </ul>
        </div>
        @elseif($vacation->status === 'rejected')
        <div class="next-steps rejected">
            <h4>🤝 Next Steps:</h4>
            <ul>
                <li>Review the reason for rejection (see HR message above if provided)</li>
                <li>Consider submitting a new request for different dates</li>
                <li>Contact HR if you have questions about this decision</li>
                <li>Check your vacation balance and company policies</li>
                <li>Plan alternative dates that might work better</li>
            </ul>
        </div>
        @endif

        <div class="contact-info">
            <h4>Questions or Need Help?</h4>
            <p>Contact HR at <strong>hr@alzohbi.com</strong> or visit the HR office.</p>
            <p>We're here to help with any questions about your vacation request.</p>
        </div>

        <div class="footer">
            <p><strong>Important:</strong> This is an automated notification. Please do not reply to this message.</p>
            <p>For questions, contact HR at hr@alzohbi.com</p>
            <hr>
            <p>&copy; {{ date('Y') }} Alzohbi. All rights reserved.</p>
        </div>
    </div>
</body>
</html>