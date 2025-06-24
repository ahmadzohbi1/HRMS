<?php


    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Vacation;
    use App\Models\Employee;
    use App\Models\VacationType;
    use App\Models\EmployeeVacationBalance;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Mail;
    use App\Mail\VacationStatusChanged;
    class VacationController extends Controller
    {
        public function index()
        {
            $vacations = Vacation::with(['employee', 'vacationType'])->latest()->get();
            return view('admin.vacations.index', compact('vacations'));
        }

        public function create()
        {
            $employees = Employee::all();
            $vacationTypes = VacationType::all();
            return view('admin.vacations.create', compact('employees', 'vacationTypes'));
        }

        public function store(Request $request)
        {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'vacation_type_id' => 'required|exists:vacation_types,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Calculate vacation days
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $daysRequested = $startDate->diffInDays($endDate) + 1;

            // Check if employee has sufficient balance
            $employee = Employee::find($request->employee_id);
            $currentYear = now()->year;

            if (!$employee->hasVacationBalance($request->vacation_type_id, $daysRequested, $currentYear)) {
                return back()->withErrors(['error' => 'Insufficient vacation balance for this request.']);
            }

            Vacation::create($request->all());

            return redirect()->route('vacations.index')
                ->with('success', 'Vacation request created successfully.');
        }

        public function show($id)
        {
            $vacation = Vacation::with(['employee', 'vacationType'])->findOrFail($id);
            return view('admin.vacations.show', compact('vacation'));
        }

        public function edit($id)
        {
            $vacation = Vacation::with(['employee', 'vacationType'])->findOrFail($id);
            $employees = Employee::all();
            $vacationTypes = VacationType::all();
            return view('admin.vacations.edit', compact('vacation', 'employees', 'vacationTypes'));
        }
        // In your VacationController update method
        public function update(Request $request, $id)
        {
            $vacation = Vacation::findOrFail($id);
            
            $request->validate([
                'employee_id' => 'nullable|exists:employees,id',
                'vacation_type_id' => 'required|exists:vacation_types,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|in:pending,approved,rejected',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $oldStatus = $vacation->status;
            $vacation->update($request->all());

            // Handle balance adjustment when status changes
            if ($oldStatus !== $request->status && $vacation->employee_id) {
                $this->handleBalanceAdjustment($vacation, $oldStatus, $request->status);
            }

            // Send email notification if status changed to approved or rejected
            if ($oldStatus !== $request->status && in_array($request->status, ['approved', 'rejected'])) {
                try {
                    $recipientEmail = $vacation->employee ? $vacation->employee->email : $vacation->applicant_email;
                    if ($recipientEmail) {
                        Mail::to($recipientEmail)->send(new VacationStatusChanged($vacation, $request->admin_notes));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send vacation status email: ' . $e->getMessage());
                    // Don't fail the update, just log the error
                }
            }

            return redirect()->route('vacations.index')
                ->with('success', 'Vacation updated successfully' . ($recipientEmail ? ' and notification email sent.' : '.'));
        }

        public function destroy(Vacation $vacation)
        {
            // If vacation was approved, restore the balance
            if ($vacation->isApproved()) {
                $balance = $vacation->employee->getVacationBalance($vacation->vacation_type_id, now()->year);
                if ($balance) {
                    $balance->addBalance($vacation->duration_in_days);
                }
            }

            $vacation->delete();

            return redirect()->route('vacations.index')
                ->with('success', 'Vacation deleted successfully.');
        }

        private function handleBalanceAdjustment(Vacation $vacation, string $oldStatus, string $newStatus)
        {
            $balance = $vacation->employee->getVacationBalance($vacation->vacation_type_id, now()->year);

            if (!$balance)
                return;

            // If changing from approved to pending/rejected, restore balance
            if ($oldStatus === 'approved' && in_array($newStatus, ['pending', 'rejected'])) {
                $balance->addBalance($vacation->duration_in_days);
            }

            // If changing from pending/rejected to approved, deduct balance
            if (in_array($oldStatus, ['pending', 'rejected']) && $newStatus === 'approved') {
                $balance->deductBalance($vacation->duration_in_days);
            }
        }
    }