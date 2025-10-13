<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounceIn 0.6s ease-out;
        }
        .icon-success { color: #28a745; }
        .icon-error { color: #dc3545; }
        .icon-warning { color: #ffc107; }
        
        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }
        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .vacation-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #6c757d;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin: 15px 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            color: #999;
            font-size: 12px;
        }
        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @if($status === 'success')
            <div class="icon icon-success">✅</div>
        @elseif($status === 'error')
            <div class="icon icon-error">❌</div>
        @else
            <div class="icon icon-warning">⚠️</div>
        @endif

        <h1>{{ $title }}</h1>
        <p class="message">{{ $message }}</p>

        @if($vacation)
            <div class="vacation-details">
                <h3 style="margin-bottom: 15px; color: #333;">Request Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Employee:</span>
                    <span class="detail-value">{{ $vacation->applicant_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Vacation Type:</span>
                    <span class="detail-value">{{ $vacation->vacationType->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Dates:</span>
                    <span class="detail-value">
                        {{ $vacation->start_date->format('M j, Y') }} - {{ $vacation->end_date->format('M j, Y') }}
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $vacation->duration_in_days }} day(s)</span>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <span class="status-badge status-{{ $vacation->status }}">
                        {{ strtoupper($vacation->status) }}
                    </span>
                </div>
            </div>
        @endif

        <a href="{{ url('/dashboard/vacations') }}" class="btn">
            Go to Admin Dashboard
        </a>

        <div class="footer">
            <p>HRMS - Human Resource Management System</p>
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</body>
</html>

