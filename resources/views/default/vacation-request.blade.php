<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacation Request Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .employee-info-card {
            background: #f8f9ff;
            border: 2px solid #e3e8ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            display: none;
        }
        
        .pin-verification {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            display: none;
        }
        
        .vacation-balance {
            background: #d1f2eb;
            border: 2px solid #a3e4d7;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #e1e8ed;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.2s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .duration-display {
            background: #e8f4fd;
            border: 2px solid #b3d9f2;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            color: #1e40af;
        }
        
        .pin-input {
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 0.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-calendar-alt me-2"></i>Vacation Request Form</h2>
                <p class="mb-0">Submit your vacation request for approval</p>
            </div>
            
            <div class="form-body">
                <form id="vacationForm" action="{{ route('vacation-request.store') }}" method="POST">
                    @csrf
                    
                    <!-- Employee Selection -->
                    <div class="form-group">
                        <label for="employee_id" class="form-label">
                            <i class="fas fa-user me-2"></i>Select Employee
                        </label>
                        <select class="form-control" id="employee_id" name="employee_id" required>
                            <option value="">Choose an employee...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" 
                                        data-name="{{ $employee->name }}"
                                        data-email="{{ $employee->email }}"
                                        data-phone="{{ $employee->phone }}"
                                        data-pin="{{ $employee->pin }}">
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Employee Info Card (Hidden initially) -->
                    <div id="employeeInfoCard" class="employee-info-card">
                        <h5><i class="fas fa-info-circle me-2"></i>Employee Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> <span id="displayName"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> <span id="displayEmail"></span>
                            </div>
                            <div class="col-md-6 mt-2">
                                <strong>Phone:</strong> <span id="displayPhone"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PIN Verification -->
                    <div id="pinVerification" class="pin-verification">
                        <h5><i class="fas fa-lock me-2"></i>PIN Verification</h5>
                        <p>Please enter your PIN to verify your identity:</p>
                        <input type="password" id="pin_input" class="form-control pin-input" 
                               placeholder="Enter PIN" maxlength="4" required>
                        <div id="pinError" class="text-danger mt-2" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-1"></i>Invalid PIN. Please try again.
                        </div>
                        <div id="pinSuccess" class="text-success mt-2" style="display: none;">
                            <i class="fas fa-check-circle me-1"></i>PIN verified successfully!
                        </div>
                    </div>
                    
                    <!-- Vacation Type Selection -->
                    <div class="form-group">
                        <label for="vacation_type_id" class="form-label">
                            <i class="fas fa-tags me-2"></i>Vacation Type
                        </label>
                        <select class="form-control" id="vacation_type_id" name="vacation_type_id" required>
                            <option value="">Choose vacation type...</option>
                            @foreach($vacationTypes as $type)
                                <option value="{{ $type->id }}">
                                    {{ $type->name }} 
                                    @if($type->is_paid) (Paid) @else (Unpaid) @endif
                                    @if($type->max_days_allowed) - Max: {{ $type->max_days_allowed }} days @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Vacation Balance Display -->
                    <div id="vacationBalance" class="vacation-balance" style="display: none;">
                        <h6><i class="fas fa-chart-bar me-2"></i>Vacation Balance</h6>
                        <div id="balanceInfo"></div>
                    </div>
                    
                    <!-- Date Selection -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Start Date
                                </label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>End Date
                                </label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Duration Display -->
                    <div id="durationDisplay" class="duration-display" style="display: none;">
                        <i class="fas fa-clock me-2"></i>
                        <span id="durationText">Duration will be calculated automatically</span>
                    </div>
                    
                    <!-- Reason -->
                    <div class="form-group">
                        <label for="reason" class="form-label">
                            <i class="fas fa-comment me-2"></i>Reason (Optional)
                        </label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                  placeholder="Please provide a reason for your vacation request..."></textarea>
                    </div>
                    
                    <!-- Hidden fields for auto-filled data -->
                    <input type="hidden" id="applicant_name" name="applicant_name">
                    <input type="hidden" id="applicant_email" name="applicant_email">
                    <input type="hidden" id="applicant_phone" name="applicant_phone">
                    <input type="hidden" name="status" value="pending">
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane me-2"></i>Submit Vacation Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let isPinVerified = false;
        let selectedEmployeePin = '';
        
        // Employee selection change
        document.getElementById('employee_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                // Show employee info
                document.getElementById('displayName').textContent = selectedOption.dataset.name;
                document.getElementById('displayEmail').textContent = selectedOption.dataset.email;
                document.getElementById('displayPhone').textContent = selectedOption.dataset.phone;
                document.getElementById('employeeInfoCard').style.display = 'block';
                
                // Fill hidden fields
                document.getElementById('applicant_name').value = selectedOption.dataset.name;
                document.getElementById('applicant_email').value = selectedOption.dataset.email;
                document.getElementById('applicant_phone').value = selectedOption.dataset.phone;
                
                // Show PIN verification
                selectedEmployeePin = selectedOption.dataset.pin;
                document.getElementById('pinVerification').style.display = 'block';
                document.getElementById('pin_input').value = '';
                document.getElementById('pinError').style.display = 'none';
                document.getElementById('pinSuccess').style.display = 'none';
                isPinVerified = false;
                
                // Reset vacation balance
                document.getElementById('vacationBalance').style.display = 'none';
                
                updateSubmitButton();
            } else {
                // Hide all sections
                document.getElementById('employeeInfoCard').style.display = 'none';
                document.getElementById('pinVerification').style.display = 'none';
                document.getElementById('vacationBalance').style.display = 'none';
                isPinVerified = false;
                updateSubmitButton();
            }
        });
        
        // PIN verification
        document.getElementById('pin_input').addEventListener('input', function() {
            const enteredPin = this.value;
            
            if (enteredPin.length === 4) {
                if (enteredPin === selectedEmployeePin) {
                    document.getElementById('pinError').style.display = 'none';
                    document.getElementById('pinSuccess').style.display = 'block';
                    isPinVerified = true;
                    updateSubmitButton();
                } else {
                    document.getElementById('pinSuccess').style.display = 'none';
                    document.getElementById('pinError').style.display = 'block';
                    isPinVerified = false;
                    updateSubmitButton();
                }
            } else {
                document.getElementById('pinError').style.display = 'none';
                document.getElementById('pinSuccess').style.display = 'none';
                isPinVerified = false;
                updateSubmitButton();
            }
        });
        
        // Vacation type change - load balance
        document.getElementById('vacation_type_id').addEventListener('change', function() {
            const employeeId = document.getElementById('employee_id').value;
            const vacationTypeId = this.value;
            
            if (employeeId && vacationTypeId && isPinVerified) {
                // Simulate API call to get vacation balance
                // In real implementation, you'd make an AJAX call to your Laravel backend
                fetch(`/api/vacation-balance/${employeeId}/${vacationTypeId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('balanceInfo').innerHTML = `
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Total Days:</strong> ${data.total_days || 'Unlimited'}
                                </div>
                                <div class="col-md-4">
                                    <strong>Used Days:</strong> ${data.used_days || 0}
                                </div>
                                <div class="col-md-4">
                                    <strong>Remaining:</strong> <span class="text-success">${data.remaining_days || 'Unlimited'}</span>
                                </div>
                            </div>
                        `;
                        document.getElementById('vacationBalance').style.display = 'block';
                    })
                    .catch(error => {
                        console.log('Balance fetch error:', error);
                        // Fallback display
                        document.getElementById('balanceInfo').innerHTML = '<p>Balance information will be verified upon submission.</p>';
                        document.getElementById('vacationBalance').style.display = 'block';
                    });
            }
        });
        
        // Date change - calculate duration
        function calculateDuration() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                if (end >= start) {
                    document.getElementById('durationText').textContent = `Duration: ${diffDays} day(s)`;
                    document.getElementById('durationDisplay').style.display = 'block';
                } else {
                    document.getElementById('durationText').textContent = 'End date must be after start date';
                    document.getElementById('durationDisplay').style.display = 'block';
                    document.getElementById('durationDisplay').style.background = '#ffeaa7';
                    document.getElementById('durationDisplay').style.color = '#d63031';
                }
            } else {
                document.getElementById('durationDisplay').style.display = 'none';
            }
            
            updateSubmitButton();
        }
        
        document.getElementById('start_date').addEventListener('change', calculateDuration);
        document.getElementById('end_date').addEventListener('change', calculateDuration);
        
        // Update submit button state
        function updateSubmitButton() {
            const employeeId = document.getElementById('employee_id').value;
            const vacationTypeId = document.getElementById('vacation_type_id').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            const isFormValid = employeeId && vacationTypeId && startDate && endDate && isPinVerified;
            
            document.getElementById('submitBtn').disabled = !isFormValid;
        }
        
        // Form submission
        document.getElementById('vacationForm').addEventListener('submit', function(e) {
            if (!isPinVerified) {
                e.preventDefault();
                alert('Please verify your PIN before submitting.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
        });
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
        document.getElementById('end_date').min = today;
    </script>
</body>
</html>