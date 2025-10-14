<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Employees\Shift;
use App\Models\Employees\Salary;
use App\Models\Holiday;
use App\Models\Vacation;
use App\Models\TimeLog;
use App\Models\TimeLogPin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if PIN is required
        $pin = TimeLogPin::getActivePin();
        
        // Check if user is unlocked for today
        $isUnlocked = $request->session()->get('timelog_unlocked_date') === Carbon::today()->toDateString();
        
        if ($pin && !$isUnlocked) {
            // Show PIN lock screen
            return view('default.pin-lock');
        }
        
        $employees = Employee::select('id', 'name', 'image_url')->get(); // Fetch only id and name
        $shifts = Shift::all();
        return view('default.home', compact('employees','shifts'));
    }
    
    /**
     * Verify PIN and unlock
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);
        
        $pin = TimeLogPin::getActivePin();
        
        if (!$pin) {
            return redirect()->back()->with('error', 'No PIN configured. Please contact administrator.');
        }
        
        if ($pin->checkPin($request->pin)) {
            // Store unlock status in session (valid for today only)
            $request->session()->put('timelog_unlocked_date', Carbon::today()->toDateString());
            
            return redirect()->route('home')->with('success', 'Access granted');
        }
        
        return redirect()->back()->with('error', 'Invalid PIN. Please try again.');
    }
    
    public function root()
    {
        $employees = Employee::count();
        
        // Get upcoming holidays (next 30 days)
        $upcomingHolidays = Holiday::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(30))
            ->orderBy('date', 'asc')
            ->get();
        
        // Get pending vacation requests
        $pendingVacations = Vacation::with(['employee', 'vacationType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get late employees today
        $today = Carbon::today();
        $lateEmployees = $this->getLateEmployeesToday();
        
        // Get current month for calendar
        $currentMonth = Carbon::now();
        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd = $currentMonth->copy()->endOfMonth();
        
        // Get holidays for current month
        $monthHolidays = Holiday::whereBetween('date', [$monthStart, $monthEnd])
            ->get()
            ->keyBy(function($holiday) {
                return Carbon::parse($holiday->date)->format('Y-m-d');
            });
        
        // Get vacations for current month
        $monthVacations = Vacation::where('status', 'approved')
            ->where(function($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('start_date', [$monthStart, $monthEnd])
                    ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                    ->orWhere(function($q) use ($monthStart, $monthEnd) {
                        $q->where('start_date', '<=', $monthStart)
                          ->where('end_date', '>=', $monthEnd);
                    });
            })
            ->with('employee')
            ->get();
        
        return view('admin.index', compact(
            'employees',
            'upcomingHolidays',
            'pendingVacations',
            'lateEmployees',
            'currentMonth',
            'monthHolidays',
            'monthVacations'
        ));
    }
    
    private function getLateEmployeesToday()
    {
        $today = Carbon::today();
        $lateEmployees = [];
        
        // Get today's time logs with time_in
        $todayLogs = TimeLog::with(['employee.position'])
            ->whereDate('date', $today)
            ->whereNotNull('time_in')
            ->get();
        
        foreach ($todayLogs as $log) {
            // Get employee's shift
            $shift = Shift::where('employee_id', $log->employee_id)
                ->orWhere('department_id', optional($log->employee->departments->first())->id)
                ->orWhere('company_shift', 1)
                ->first();
            
            if ($shift) {
                $expectedTime = Carbon::parse($shift->time_in);
                $actualTime = Carbon::parse($log->time_in);
                
                // Consider late if more than 5 minutes after shift start
                if ($actualTime->diffInMinutes($expectedTime, false) > 5) {
                    $lateEmployees[] = [
                        'employee' => $log->employee,
                        'expected_time' => $expectedTime->format('H:i'),
                        'actual_time' => $actualTime->format('H:i'),
                        'late_by' => $actualTime->diffInMinutes($expectedTime) . ' mins'
                    ];
                }
            }
        }
        
        return collect($lateEmployees);
    }
    
    private function getMonthlySalaryData()
    {
        $salaryData = [];
        $months = [];
        
        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->format('M Y');
            
            // Get all active salaries
            $activeSalaries = Salary::with(['advances', 'bonuses'])
                ->where('status', 'active')
                ->get();
            
            $totalMonthlySalary = 0;
            
            foreach ($activeSalaries as $salary) {
                // Calculate advances for this specific month
                $monthlyAdvances = $salary->advances()
                    ->whereMonth('advance_date', $date->month)
                    ->whereYear('advance_date', $date->year)
                    ->whereIn('status', ['approved', 'paid'])
                    ->sum('amount');
                
                // Calculate bonuses for this specific month
                $monthlyBonuses = $salary->bonuses()
                    ->whereMonth('bonus_date', $date->month)
                    ->whereYear('bonus_date', $date->year)
                    ->whereIn('status', ['approved', 'paid'])
                    ->sum('amount');
                
                // Calculate net salary for this employee for this month
                $netSalary = $salary->fixed_salary + $monthlyBonuses - $monthlyAdvances;
                $totalMonthlySalary += $netSalary;
            }
            
            $months[] = $monthName;
            $salaryData[] = $totalMonthlySalary;
        }
        
        return [
            'months' => $months,
            'salaries' => $salaryData
        ];
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}