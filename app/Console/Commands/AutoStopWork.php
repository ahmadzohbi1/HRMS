<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\TimeLog;
use App\Models\Employees\Shift;
use App\Http\Controllers\Admin\TimeSheetController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AutoStopWork extends Command
{
    protected $signature = 'work:auto-stop 
                            {--dry-run : Show what would be done without actually doing it}
                            {--force : Run even outside normal hours}';

    protected $description = 'Automatically stop work for employees who forgot to clock out 10 minutes after shift end';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now();
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');
        
        // Skip execution outside working hours unless forced (saves server resources)
        if (!$isForced && ($currentTime->hour < 6 || $currentTime->hour > 23)) {
            return 0; // Silent exit outside working hours
        }
        
        if ($isDryRun) {
            $this->info("DRY RUN MODE - No actual changes will be made");
        }
        
        try {
            // Get all employees who have clocked in today but haven't clocked out
            $activeTimeLogs = TimeLog::whereDate('date', $today)
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->get();

            if ($activeTimeLogs->isEmpty()) {
                // Only log this in verbose mode to reduce log noise
                if ($this->getOutput()->isVerbose()) {
                    Log::info("No active time logs found for auto-stop check");
                }
                return 0;
            }

            $stoppedCount = 0;
            $checkedCount = $activeTimeLogs->count();

            foreach ($activeTimeLogs as $timeLog) {
                $employee = Employee::find($timeLog->employee_id);
                
                if (!$employee) {
                    Log::warning("Employee not found for time log ID {$timeLog->id}");
                    continue;
                }

                // Get the employee's shift
                $employeeShift = Shift::where('employee_id', $employee->id)->first();
                $shift = $employeeShift ?: Shift::where('company_shift', 1)->first();

                if (!$shift || !$shift->time_out) {
                    Log::warning("No shift or time_out found for employee {$employee->id} ({$employee->name})");
                    continue;
                }

                // Parse shift end time and add 10 minutes
                $today = Carbon::today();
                $shiftEndDateTime = $today->copy()->setTimeFromTimeString($shift->time_out);
                $autoStopDateTime = $shiftEndDateTime->copy()->addMinutes(10);

                // Check if current time is past the auto-stop time
                if ($currentTime >= $autoStopDateTime) {
                    if ($isDryRun) {
                        $this->line("Would auto-stop work for {$employee->name} (ID: {$employee->id}) - Shift ends at {$shift->time_out}");
                    } else {
                        $this->autoStopEmployee($timeLog, $shift, $employee);
                    }
                    $stoppedCount++;
                }
            }

            // Only log if actions were taken or in verbose mode
            if ($stoppedCount > 0 || $this->getOutput()->isVerbose()) {
                $action = $isDryRun ? "Would auto-stop" : "Auto-stopped";
                $message = "{$action} work for {$stoppedCount} out of {$checkedCount} active employees";
                Log::info($message);
                
                if ($this->getOutput()->isVerbose()) {
                    $this->info($message);
                }
            }

        } catch (\Exception $e) {
            Log::error("Auto-stop command failed: " . $e->getMessage());
            
            // In production, you might want to send alerts here
            // Mail::to('admin@company.com')->send(new AutoStopFailedMail($e));
            
            return 1; // Exit with error code
        }

        return 0;
    }

    private function autoStopEmployee($timeLog, $shift, $employee)
    {
        try {
            // Set time_out to shift end time
            $timeLog->update([
                'time_out' => $shift->time_out
            ]);

            Log::info("Auto-stopped work for employee {$employee->id} ({$employee->name}) at shift end time {$shift->time_out}");

            // Update timesheet and salary
            $timesheetController = new TimeSheetController();
            $timesheetController->updateTimesheetEmployee($employee->id, Carbon::now()->month, Carbon::now()->year);
            $timesheetController->updateSalary($employee->id);

            if ($this->getOutput()->isVerbose()) {
                $this->line("✓ Auto-stopped work for {$employee->name} (ID: {$employee->id}) at {$shift->time_out}");
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to auto-stop work for employee {$employee->id}: " . $e->getMessage());
            
            if ($this->getOutput()->isVerbose()) {
                $this->error("✗ Failed to auto-stop work for {$employee->name}: " . $e->getMessage());
            }
        }
    }
}