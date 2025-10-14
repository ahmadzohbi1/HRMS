<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Log - Enter PIN</title>
    
    <!-- Bootstrap CSS -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow: hidden;
        }

        .pin-lock-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px;
            max-width: 450px;
            width: 90%;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lock-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
            }
        }

        .lock-icon i {
            font-size: 48px;
            color: white;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 12px;
            text-align: center;
        }

        .subtitle {
            color: #718096;
            font-size: 16px;
            margin-bottom: 40px;
            text-align: center;
        }

        .pin-input-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .pin-digit {
            width: 60px;
            height: 60px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            color: #2d3748;
            transition: all 0.3s;
            background: #f7fafc;
        }

        .pin-digit:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: scale(1.05);
        }

        .pin-digit.filled {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #fc8181;
        }

        .time-display {
            text-align: center;
            font-size: 48px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            font-variant-numeric: tabular-nums;
        }

        .date-display {
            text-align: center;
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
        }

        .admin-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .admin-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .admin-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="pin-lock-container">
        <div class="lock-icon">
            <i class="bx bx-lock-alt"></i>
        </div>

        <div class="time-display" id="currentTime"></div>
        <div class="date-display" id="currentDate"></div>

        <h1>Time Log System</h1>
        <p class="subtitle">Enter 4-digit PIN to access</p>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bx bx-error-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('verify-pin') }}" method="POST" id="pinForm">
            @csrf
            <div class="pin-input-container">
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" id="digit1" autofocus>
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" id="digit2">
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" id="digit3">
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" id="digit4">
            </div>

            <input type="hidden" name="pin" id="pinValue">

            <button type="submit" class="submit-btn" id="submitBtn" disabled>
                <i class="bx bx-lock-open-alt"></i> Unlock
            </button>
        </form>

        <div class="admin-link">
            <a href="{{ route('login') }}">
                <i class="bx bx-shield"></i> Admin Login
            </a>
        </div>
    </div>

    <script>
        const pinInputs = document.querySelectorAll('.pin-digit');
        const pinValue = document.getElementById('pinValue');
        const submitBtn = document.getElementById('submitBtn');

        // Handle input on PIN digits
        pinInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Only allow numbers
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                if (e.target.value) {
                    e.target.classList.add('filled');
                    
                    // Auto-focus next input
                    if (index < 3) {
                        pinInputs[index + 1].focus();
                    }
                } else {
                    e.target.classList.remove('filled');
                }
                
                // Update hidden PIN value
                updatePinValue();
            });

            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                    pinInputs[index - 1].value = '';
                    pinInputs[index - 1].classList.remove('filled');
                    updatePinValue();
                }
                
                // Submit on Enter if PIN is complete
                if (e.key === 'Enter' && getPinValue().length === 4) {
                    e.preventDefault();
                    document.getElementById('pinForm').submit();
                }
            });
        });

        function getPinValue() {
            let pin = '';
            pinInputs.forEach(input => {
                pin += input.value;
            });
            return pin;
        }

        function updatePinValue() {
            const pin = getPinValue();
            pinValue.value = pin;
            submitBtn.disabled = pin.length !== 4;
            
            // Auto-submit when 4 digits are entered
            if (pin.length === 4) {
                setTimeout(() => {
                    document.getElementById('pinForm').submit();
                }, 500);
            }
        }

        // Update time and date
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: false 
            });
            const dateStr = now.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            document.getElementById('currentTime').textContent = timeStr;
            document.getElementById('currentDate').textContent = dateStr;
        }

        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>

