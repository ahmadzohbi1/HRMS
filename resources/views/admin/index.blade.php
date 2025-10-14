@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('css')
    <style>
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f46a6a;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        .calendar-wrapper {
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            min-height: 50px;
        }
        
        .calendar-day:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .calendar-day.other-month {
            color: #adb5bd;
            background: #fafafa;
        }
        
        .calendar-day.today {
            background: #556ee6;
            color: white;
            font-weight: bold;
        }
        
        .calendar-day.holiday {
            background: #f46a6a;
            color: white;
        }
        
        .calendar-day.vacation {
            background: #34c38f;
            color: white;
        }
        
        .calendar-day-indicator {
            position: absolute;
            bottom: 2px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: currentColor;
        }
        
        .list-item {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }
        
        .list-item:hover {
            background: #f8f9fa;
        }
        
        .list-item:last-child {
            border-bottom: none;
        }
        
        .badge-date {
            background: #f8f9fa;
            color: #495057;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .scrollable-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #adb5bd;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('title') Dashboard Overview @endslot
    @endcomponent

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Total Employees</p>
                            <h4 class="mb-0">{{ $employees }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="stat-icon bg-primary bg-soft text-primary">
                                <i class="bx bx-user font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Upcoming Holidays</p>
                            <h4 class="mb-0">{{ $upcomingHolidays->count() }}</h4>
                            <p class="text-muted mb-0" style="font-size: 12px;">Next 30 days</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="stat-icon bg-success bg-soft text-success">
                                <i class="bx bx-calendar-event font-size-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Pending Requests</p>
                            <h4 class="mb-0">{{ $pendingVacations->count() }}</h4>
                            <p class="text-muted mb-0" style="font-size: 12px;">Vacation requests</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center position-relative">
                            <div class="stat-icon bg-warning bg-soft text-warning">
                                <i class="bx bx-time-five font-size-24"></i>
                            </div>
                            @if($pendingVacations->count() > 0)
                                <span class="notification-badge">{{ $pendingVacations->count() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium mb-2">Late Today</p>
                            <h4 class="mb-0">{{ $lateEmployees->count() }}</h4>
                            <p class="text-muted mb-0" style="font-size: 12px;">Employees</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center position-relative">
                            <div class="stat-icon bg-danger bg-soft text-danger">
                                <i class="bx bx-user-x font-size-24"></i>
                            </div>
                            @if($lateEmployees->count() > 0)
                                <span class="notification-badge">{{ $lateEmployees->count() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calendar -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bx bx-calendar me-2"></i>Calendar - {{ $currentMonth->format('F Y') }}
                    </h4>
                    
                    <div class="calendar-wrapper">
                        <div class="calendar-grid">
                            <!-- Day headers -->
                            <div class="calendar-day-header">Sun</div>
                            <div class="calendar-day-header">Mon</div>
                            <div class="calendar-day-header">Tue</div>
                            <div class="calendar-day-header">Wed</div>
                            <div class="calendar-day-header">Thu</div>
                            <div class="calendar-day-header">Fri</div>
                            <div class="calendar-day-header">Sat</div>
                            
                            @php
                                $startOfMonth = $currentMonth->copy()->startOfMonth();
                                $endOfMonth = $currentMonth->copy()->endOfMonth();
                                $startDate = $startOfMonth->copy()->startOfWeek();
                                $endDate = $endOfMonth->copy()->endOfWeek();
                                $today = \Carbon\Carbon::today();
                            @endphp
                            
                            @for($date = $startDate->copy(); $date->lte($endDate); $date->addDay())
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $isToday = $date->isSameDay($today);
                                    $isOtherMonth = $date->month !== $currentMonth->month;
                                    $isHoliday = isset($monthHolidays[$dateStr]);
                                    
                                    $isVacation = $monthVacations->contains(function($vacation) use ($date) {
                                        return $date->between(
                                            \Carbon\Carbon::parse($vacation->start_date)->startOfDay(),
                                            \Carbon\Carbon::parse($vacation->end_date)->endOfDay()
                                        );
                                    });
                                    
                                    $classes = ['calendar-day'];
                                    if ($isOtherMonth) $classes[] = 'other-month';
                                    if ($isToday) $classes[] = 'today';
                                    elseif ($isHoliday) $classes[] = 'holiday';
                                    elseif ($isVacation) $classes[] = 'vacation';
                                    
                                    $title = '';
                                    if ($isHoliday) $title = $monthHolidays[$dateStr]->name;
                                @endphp
                                
                                <div class="{{ implode(' ', $classes) }}" title="{{ $title }}">
                                    {{ $date->day }}
                                </div>
                            @endfor
                        </div>
                        
                        <div class="mt-3 d-flex gap-3 justify-content-center" style="font-size: 12px;">
                            <div><span class="badge bg-primary">Today</span></div>
                            <div><span class="badge bg-danger">Holiday</span></div>
                            <div><span class="badge bg-success">Vacation</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bx bx-calendar-star me-2"></i>Upcoming Holidays
                    </h4>
                    
                    @if($upcomingHolidays->count() > 0)
                        <div class="scrollable-list">
                            @foreach($upcomingHolidays as $holiday)
                                @php
                                    $holidayDate = \Carbon\Carbon::parse($holiday->date);
                                    $daysUntil = \Carbon\Carbon::today()->diffInDays($holidayDate, false);
                                @endphp
                                <div class="list-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-danger bg-soft text-danger">
                                                    <i class="bx bx-calendar-event"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="font-size-14 mb-1">{{ $holiday->name }}</h5>
                                            <p class="text-muted mb-0" style="font-size: 12px;">
                                                {{ $holidayDate->format('l, F j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        @if($daysUntil == 0)
                                            <span class="badge bg-danger">Today</span>
                                        @elseif($daysUntil == 1)
                                            <span class="badge bg-warning">Tomorrow</span>
                                        @else
                                            <span class="badge-date">{{ $daysUntil }} days</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bx bx-calendar-x"></i>
                            <p>No upcoming holidays in the next 30 days</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Vacation Requests -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bx bx-file me-2"></i>Pending Vacation Requests
                        @if($pendingVacations->count() > 0)
                            <span class="badge bg-warning ms-2">{{ $pendingVacations->count() }}</span>
                        @endif
                    </h4>
                    
                    @if($pendingVacations->count() > 0)
                        <div class="scrollable-list">
                            @foreach($pendingVacations as $vacation)
                                @php
                                    $startDate = \Carbon\Carbon::parse($vacation->start_date);
                                    $endDate = \Carbon\Carbon::parse($vacation->end_date);
                                    $duration = $startDate->diffInDays($endDate) + 1;
                                @endphp
                                <div class="list-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar-xs flex-shrink-0">
                                                    <span class="avatar-title rounded-circle bg-warning bg-soft text-warning">
                                                        <i class="bx bx-user"></i>
                                                    </span>
                                                </div>
                                                <div class="ms-2">
                                                    <h5 class="font-size-14 mb-0">{{ $vacation->employee_name ?? 'N/A' }}</h5>
                                                    <p class="text-muted mb-0" style="font-size: 11px;">
                                                        {{ optional($vacation->vacationType)->name ?? 'Vacation' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="ms-4">
                                                <p class="mb-1" style="font-size: 13px;">
                                                    <i class="bx bx-calendar text-muted me-1"></i>
                                                    <strong>From:</strong> {{ $startDate->format('M j, Y') }}
                                                </p>
                                                <p class="mb-1" style="font-size: 13px;">
                                                    <i class="bx bx-calendar-check text-muted me-1"></i>
                                                    <strong>To:</strong> {{ $endDate->format('M j, Y') }}
                                                </p>
                                                <p class="mb-0" style="font-size: 13px;">
                                                    <i class="bx bx-time text-muted me-1"></i>
                                                    <strong>Duration:</strong> {{ $duration }} day{{ $duration > 1 ? 's' : '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('vacations.index') }}" class="btn btn-sm btn-outline-primary">
                                                Review
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bx bx-check-circle"></i>
                            <p>No pending vacation requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Late Employees Today -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bx bx-alarm-exclamation me-2"></i>Late Employees Today
                        @if($lateEmployees->count() > 0)
                            <span class="badge bg-danger ms-2">{{ $lateEmployees->count() }}</span>
                        @endif
                    </h4>
                    
                    @if($lateEmployees->count() > 0)
                        <div class="scrollable-list">
                            @foreach($lateEmployees as $late)
                                <div class="list-item">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-danger bg-soft text-danger">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="font-size-14 mb-1">{{ $late['employee']->name }}</h5>
                                            <p class="text-muted mb-0" style="font-size: 12px;">
                                                {{ optional($late['employee']->position)->name ?? 'Employee' }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-end">
                                            <div class="mb-1">
                                                <span class="text-muted" style="font-size: 11px;">Expected:</span>
                                                <strong style="font-size: 13px;">{{ $late['expected_time'] }}</strong>
                                            </div>
                                            <div class="mb-1">
                                                <span class="text-muted" style="font-size: 11px;">Arrived:</span>
                                                <strong class="text-danger" style="font-size: 13px;">{{ $late['actual_time'] }}</strong>
                                            </div>
                                            <span class="badge bg-danger">Late by {{ $late['late_by'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bx bx-happy-heart-eyes"></i>
                            <p>All employees are on time today!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Optional: Add any interactive functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Calendar day click handler (optional)
            document.querySelectorAll('.calendar-day:not(.other-month)').forEach(day => {
                day.addEventListener('click', function() {
                    // You can add modal or detailed view here
                    console.log('Clicked day:', this.textContent);
                });
            });
        });
    </script>
@endsection
