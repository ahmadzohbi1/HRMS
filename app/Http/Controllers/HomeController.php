<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Employees\Shift;
use App\Models\Employees\Salary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::select('id', 'name', 'image_url')->get(); // Fetch only id and name
        $shifts = Shift::all();
        return view('default.home', compact('employees','shifts'));
    }
    
    public function root()
    {
        $employees = Employee::count();
        $maleCount = Employee::where('gender', 'Male')->count();
        $femaleCount = Employee::where('gender', 'Female')->count();
        
        // Get monthly salary data for the last 12 months
        $monthlySalaryData = $this->getMonthlySalaryData();
        
        return view('admin.index', compact('employees', 'maleCount', 'femaleCount', 'monthlySalaryData'));
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