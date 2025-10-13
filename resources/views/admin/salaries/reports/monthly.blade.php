<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Salary Report - {{ $salary->employee->name }} - {{ $currentDate->format('F Y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin/salary-reports.css') }}" rel="stylesheet">
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <div class="company-header">
            <h1>HRMS</h1>
            <p class="mb-0">Human Resource Management System</p>
        </div>

        <!-- Report Title -->
        <div class="report-title text-center">
            <h2 class="mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Monthly Salary Report
            </h2>
            <p class="mb-0">{{ $currentDate->format('F Y') }}</p>
        </div>

        <!-- Employee Information -->
        <div class="employee-info">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">Employee Information</h5>
                    <p><strong>Name:</strong> {{ $salary->employee->name }}</p>
                    <p><strong>Email:</strong> {{ $salary->employee->email }}</p>
                    <p><strong>Phone:</strong> {{ $salary->employee->phone ?? 'N/A' }}</p>
                    @if($salary->employee->position)
                        <p><strong>Position:</strong> {{ $salary->employee->position->name }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary">Salary Information</h5>
                    <p><strong>Fixed Salary:</strong> ${{ number_format($salary->fixed_salary, 2) }}</p>
                    <p><strong>Effective From:</strong> {{ $salary->effective_date->format('F Y') }}</p>
                    <p><strong>Version:</strong> {{ $salary->version }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $salary->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($salary->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Time Logs Section -->
        <h4 class="mt-4 mb-3 text-primary">
            <i class="fas fa-clock me-2"></i>
            Time Logs for {{ $currentDate->format('F Y') }}
        </h4>
        
        @if($timeLogs && $timeLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-bordered timelogs-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalSeconds = 0;
                        @endphp
                        @foreach($timeLogs as $log)
                            @php
                                $hoursWorked = 'N/A';
                                if ($log->time_in && $log->time_out) {
                                    $timeIn = \Carbon\Carbon::parse($log->time_in);
                                    $timeOut = \Carbon\Carbon::parse($log->time_out);
                                    $seconds = $timeIn->diffInSeconds($timeOut);
                                    $totalSeconds += $seconds;
                                    $hours = floor($seconds / 3600);
                                    $minutes = floor(($seconds % 3600) / 60);
                                    $hoursWorked = sprintf('%02d:%02d', $hours, $minutes);
                                }
                            @endphp
                            <tr>
                                <td>{{ $log->date->format('M d, Y') }}</td>
                                <td>{{ $log->date->format('l') }}</td>
                                <td>{{ $log->time_in ?? 'N/A' }}</td>
                                <td>{{ $log->time_out ?? 'Still Working' }}</td>
                                <td>{{ $hoursWorked }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="4" class="text-end"><strong>Total Hours This Month:</strong></td>
                            <td><strong>{{ sprintf('%02d:%02d', floor($totalSeconds / 3600), floor(($totalSeconds % 3600) / 60)) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                No time logs recorded for this month.
            </div>
        @endif

        <!-- Salary Breakdown -->
        <h4 class="mt-4 mb-3 text-primary">
            <i class="fas fa-calculator me-2"></i>
            Salary Calculation
        </h4>
        
        <table class="table table-bordered summary-table">
            <tr>
                <td width="70%"><strong>Fixed Monthly Salary</strong></td>
                <td class="text-end"><strong>${{ number_format($breakdown['fixed_salary'], 2) }}</strong></td>
            </tr>
            
            <!-- Bonuses -->
            @if($breakdown['bonuses']->count() > 0)
                <tr class="table-success">
                    <td colspan="2"><strong>Bonuses:</strong></td>
                </tr>
                @foreach($breakdown['bonuses'] as $bonus)
                    <tr>
                        <td class="ps-4">{{ $bonus->reason ?? ucfirst($bonus->type) }} ({{ $bonus->bonus_date->format('M d') }})</td>
                        <td class="text-end text-success">+${{ number_format($bonus->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="table-success">
                    <td class="text-end"><strong>Total Bonuses:</strong></td>
                    <td class="text-end"><strong>+${{ number_format($breakdown['bonuses_total'], 2) }}</strong></td>
                </tr>
            @endif
            
            <!-- Advances -->
            @if($breakdown['advances']->count() > 0)
                <tr class="table-danger">
                    <td colspan="2"><strong>Advances:</strong></td>
                </tr>
                @foreach($breakdown['advances'] as $advance)
                    <tr>
                        <td class="ps-4">{{ $advance->reason ?? 'Advance' }} ({{ $advance->advance_date->format('M d') }})</td>
                        <td class="text-end text-danger">-${{ number_format($advance->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="table-danger">
                    <td class="text-end"><strong>Total Advances:</strong></td>
                    <td class="text-end"><strong>-${{ number_format($breakdown['advances_total'], 2) }}</strong></td>
                </tr>
            @endif
            
            <!-- Deductions -->
            @if($breakdown['deductions']->count() > 0)
                <tr class="table-warning">
                    <td colspan="2"><strong>Deductions:</strong></td>
                </tr>
                @foreach($breakdown['deductions'] as $deduction)
                    <tr>
                        <td class="ps-4">{{ $deduction->reason }} ({{ $deduction->deduction_date->format('M d') }})</td>
                        <td class="text-end text-danger">-${{ number_format($deduction->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="table-warning">
                    <td class="text-end"><strong>Total Deductions:</strong></td>
                    <td class="text-end"><strong>-${{ number_format($breakdown['deductions_total'], 2) }}</strong></td>
                </tr>
            @endif
            
            <!-- Net Salary -->
            <tr class="total-row">
                <td><strong>NET SALARY FOR {{ strtoupper($currentDate->format('F Y')) }}</strong></td>
                <td class="text-end"><strong>${{ number_format($breakdown['net_salary'], 2) }}</strong></td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="mt-5 pt-3 border-top">
            <div class="row">
                <div class="col-6">
                    <p><strong>Generated On:</strong> {{ now()->format('F d, Y g:i A') }}</p>
                    <p><strong>Generated By:</strong> {{ auth()->user()->name }}</p>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-5">_______________________</p>
                    <p><strong>Authorized Signature</strong></p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print"></i> Print Report
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

