<?php


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VacationRequestController;
use App\Http\Controllers\Admin\{
    SuperAdminController,
    ProfileController,
    AdminsController,
    RolesController,
    DepartmentController,
    EmployeeController,
    PositionsController,
    TimeSheetController,
    VacationController,
    VacationTypeController,
    EmployeeVacationBalanceController,
    HolidayController
};
use App\Http\Controllers\Admin\Employees\{
    SalaryController,
    WarningController,
    ShiftController,
    ShiftRuleController
};

Auth::routes();

Route::group(["prefix" => 'dashboard'], function () {
    Route::group(['middleware' => 'auth'], function () {
        /* ================== USER ROUTES ================== */
        //profile
        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        /* ================== ADMIN ROUTES ================== */
        Route::group(['middleware' => 'admin'], function () {
            Route::get('/', [HomeController::class, 'root'])->name('root');
            //Companies Routes
            Route::prefix('companies')->name('companies.')->group(function () {
                Route::get('/', [SuperAdminController::class, 'index'])
                    ->middleware('permission:view companies')
                    ->name('index');
                Route::get('/create', [SuperAdminController::class, 'create'])
                    ->middleware('permission:view companies')
                    ->name('create');
                Route::post('/store', [SuperAdminController::class, 'store'])
                    ->name('store');
            });
            /* ================== Admins ROUTES ================== */
            Route::prefix('admins')->name('admins.')->group(function () {
                Route::get('/', [AdminsController::class, 'index'])
                    ->middleware('permission:view admins')
                    ->name('index');
                Route::get('view/{user}', [AdminsController::class, 'show'])
                    ->middleware('permission:view admins')
                    ->name('show');
                Route::get('create', [AdminsController::class, 'create'])
                    ->middleware('permission:create admins')
                    ->name('create');
                Route::post('/', [AdminsController::class, 'store'])
                    ->middleware('permission:create admins')
                    ->name('store');
                Route::get('{user}/edit', [AdminsController::class, 'edit'])
                    ->middleware('permission:edit admins')
                    ->name('edit');
                Route::put('{user}', [AdminsController::class, 'update'])
                    ->middleware('permission:edit admins')
                    ->name('update');

                Route::post('toggle-ban', [AdminsController::class, 'toggleBan'])
                    ->name('toggle-ban');
            });
            /* ================== Roles ROUTES ================== */
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [RolesController::class, 'index'])
                    ->middleware('permission:view roles')
                    ->name('index');
                Route::get('/create', [RolesController::class, 'create'])
                    ->middleware('permission:create roles')
                    ->name('create');
                Route::post('/', [RolesController::class, 'store'])
                    ->middleware('permission:create roles')
                    ->name('store');
                Route::get('/{role}/edit', [RolesController::class, 'edit'])
                    ->middleware('permission:edit roles')
                    ->name('edit');
                Route::put('/{role}', [RolesController::class, 'update'])
                    ->middleware('permission:edit roles')
                    ->name('update');
                Route::delete('/{role}', [RolesController::class, 'destroy'])
                    ->middleware('permission:delete roles')
                    ->name('destroy');
            });
            /* ================== Employees ROUTES ================== */
            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [EmployeeController::class, 'index'])
                    ->middleware('permission:view employees')
                    ->name('index');
                Route::get('/employee/{employee}', [EmployeeController::class, 'show'])
                    ->middleware('permission:view employees')
                    ->name('show');
                Route::get('/create', [EmployeeController::class, 'create'])
                    ->middleware('permission:create employees')
                    ->name('create');
                Route::post('/store', [EmployeeController::class, 'store'])
                    ->middleware('permission:create employees')
                    ->name('store');
                Route::get('/employee/edit/{employee}', [EmployeeController::class, 'edit'])
                    ->middleware('permission:edit employees')
                    ->name('edit');
                Route::put('/employee/{id}]update', [EmployeeController::class, 'update'])
                    ->middleware('permission:edit employees')
                    ->name('update');
                Route::get('/{employee}/departments', [EmployeeController::class, 'show_department'])
                    ->middleware('permission:view employees')
                    ->name('department.index');
            });
            /* ================== TimeLogs ROUTES ================== */
            Route::prefix('employee')->name('employee.timelogs.')->group(function () {
                Route::get('/{id}/timelogs', [TimeSheetController::class, 'show_employee_timesheet'])
                    ->middleware('permission:view employees')
                    ->name('index');
                Route::post('/{id}/timelogs/update', [TimeSheetController::class, 'update'])
                    ->middleware('permission:edit employees')
                    ->name('update');
                Route::post('/{id}/timelogs/store', [TimeSheetController::class, 'store'])
                    ->middleware('permission:create employees')
                    ->name('store');
                Route::post('/timesheet/update-all-hours', [TimeSheetController::class, 'updateAllHours'])
                    ->middleware('permission:edit employees')
                    ->name('updateAllHours');
            });
            /* ================== Departments ROUTES ================== */
            Route::prefix('departments')->name('departments.')->group(function () {
                Route::get('/', [DepartmentController::class, 'index'])
                    ->middleware('permission:view departments')
                    ->name('index');
                Route::get('/create', [DepartmentController::class, 'create'])
                    ->middleware('permission:create departments')
                    ->name('create');
                Route::post('/', [DepartmentController::class, 'store'])
                    ->middleware('permission:create departments')
                    ->name('store');
                Route::get('/{id}/edit', [DepartmentController::class, 'edit'])
                    ->middleware('permission:edit departments')
                    ->name('edit');
                Route::put('/{id}', [DepartmentController::class, 'update'])
                    ->middleware('permission:edit departments')
                    ->name('update');
                Route::delete('/{id}/delete', [DepartmentController::class, 'destroy'])
                    ->middleware('permission:delete departments')
                    ->name('destroy');
            });
            /* ================== Positions ROUTES ================== */
            Route::prefix('positions')->name('positions.')->group(function () {
                Route::get('/', [PositionsController::class, 'index'])
                    ->middleware('permission:view positions')
                    ->name('index');
                Route::get('/create', [PositionsController::class, 'create'])
                    ->middleware('permission:create positions')
                    ->name('create');
                Route::post('/', [PositionsController::class, 'store'])
                    ->middleware('permission:create positions')
                    ->name('store');
                Route::get('/{id}', [PositionsController::class, 'edit'])
                    ->middleware('permission:edit positions')
                    ->name('edit');
                Route::post('/update/{id}', [PositionsController::class, 'update'])
                    ->middleware('permission:edit positions')
                    ->name('update');
                Route::delete('/delete/{id}', [PositionsController::class, 'delete'])
                    ->middleware('permission:delete positions')
                    ->name('delete');
            });
            /* ================== Salaries ROUTES ================== */
            Route::prefix('salaries')->name('salaries.')->group(function () {
                // Main Salary Routes
                Route::get('/', [SalaryController::class, 'index'])
                    ->middleware('permission:view salaries')
                    ->name('index');
                Route::get('/create', [SalaryController::class, 'create'])
                    ->middleware('permission:create salaries')
                    ->name('create');
                Route::post('/', [SalaryController::class, 'store'])
                    ->middleware('permission:create salaries')
                    ->name('store');
                Route::get('/{salary}', [SalaryController::class, 'show'])
                    ->middleware('permission:view salaries')
                    ->name('show');
                Route::get('/{salary}/edit', [SalaryController::class, 'edit'])
                    ->middleware('permission:edit salaries')
                    ->name('edit');
                Route::put('/{salary}', [SalaryController::class, 'update'])
                    ->middleware('permission:edit salaries')
                    ->name('update');
                Route::delete('/{salary}', [SalaryController::class, 'destroy'])
                    ->middleware('permission:delete salaries')
                    ->name('destroy');
                // Advance Routes (nested under salaries)
                Route::prefix('{salary}/advances')->name('advances.')->group(function () {
                    Route::get('/create', [SalaryController::class, 'createAdvance'])
                        ->middleware('permission:create salaries')
                        ->name('create');
                    Route::post('/', [SalaryController::class, 'storeAdvance'])
                        ->middleware('permission:create salaries')
                        ->name('store');
                    Route::get('/{advance}/edit', [SalaryController::class, 'editAdvance'])
                        ->middleware('permission:edit salaries')
                        ->name('edit');
                    Route::put('/{advance}', [SalaryController::class, 'updateAdvance'])
                        ->middleware('permission:edit salaries')
                        ->name('update');
                    Route::delete('/{advance}', [SalaryController::class, 'destroyAdvance'])
                        ->middleware('permission:delete salaries')
                        ->name('destroy');
                });

                // Bonus Routes (nested under salaries)
                Route::prefix('{salary}/bonuses')->name('bonuses.')->group(function () {
                    Route::get('/create', [SalaryController::class, 'createBonus'])
                        ->middleware('permission:create salaries')
                        ->name('create');
                    Route::post('/', [SalaryController::class, 'storeBonus'])
                        ->middleware('permission:create salaries')
                        ->name('store');
                    Route::get('/{bonus}/edit', [SalaryController::class, 'editBonus'])
                        ->middleware('permission:edit salaries')
                        ->name('edit');
                    Route::put('/{bonus}', [SalaryController::class, 'updateBonus'])
                        ->middleware('permission:edit salaries')
                        ->name('update');
                    Route::delete('/{bonus}', [SalaryController::class, 'destroyBonus'])
                        ->middleware('permission:delete salaries')
                        ->name('destroy');
                });
            });

            /* ================== Shifts ROUTES ================== */
            Route::prefix('shifts')->name('shifts.')->group(function () {
                Route::get('/', [ShiftController::class, 'index'])
                    ->middleware('permission:view shifts')
                    ->name('index');
                Route::get('/create', [ShiftController::class, 'create'])
                    ->middleware('permission:create shifts')
                    ->name('create');
                Route::post('/', [ShiftController::class, 'store'])
                    ->middleware('permission:create shifts')
                    ->name('store');
                Route::get('/{shift}/edit', [ShiftController::class, 'edit'])
                    ->middleware('permission:edit shifts')
                    ->name('edit');
                Route::put('/{shift}', [ShiftController::class, 'update'])
                    ->middleware('permission:edit shifts')
                    ->name('update');
                Route::delete('/{shift}', [ShiftController::class, 'destroy'])
                    ->middleware('permission:delete shifts')
                    ->name('destroy');
                Route::get('/{shift}/rules', [ShiftController::class, 'showRules'])
                    ->middleware('permission:view shifts')
                    ->name('show');
            });
            /* ================== Shift_Rules ROUTES ================== */
            Route::prefix('shifts-rules')->name('shifts-rules.')->group(function () {
                Route::get('/', [ShiftRuleController::class, 'index'])
                    ->middleware('permission:view shift_rules')
                    ->name('index');
                Route::get('/create', [ShiftRuleController::class, 'create'])
                    ->middleware('permission:create shift_rules')
                    ->name('create');
                Route::post('/store', [ShiftRuleController::class, 'store'])
                    ->middleware('permission:create shift_rules')
                    ->name('store');
                Route::get('/{id}/edit', [ShiftRuleController::class, 'edit'])
                    ->middleware('permission:edit shift_rules')
                    ->name('edit');
                Route::put('/{id}', [ShiftRuleController::class, 'update'])
                    ->middleware('permission:edit shift_rules')
                    ->name('update');
                Route::delete('/{id}/delete', [ShiftRuleController::class, 'destroy'])
                    ->middleware('permission:delete shift_rules')
                    ->name('destroy');
            });
            /* ================== Warnings ROUTES ================== */
            Route::prefix('warnings')->name('warnings.')->group(function () {
                Route::get('/', [WarningController::class, 'index'])
                    ->middleware('permission:view warnings')
                    ->name('index');
                Route::get('/show/{id}', [WarningController::class, 'show'])
                    ->middleware('permission:view warnings')
                    ->name('show');
                Route::get('/create', [WarningController::class, 'create'])
                    ->middleware('permission:create warnings')
                    ->name('create');
                Route::post('/', [WarningController::class, 'store'])
                    ->middleware('permission:create warnings')
                    ->name('store');
                Route::get('/{id}/edit', [WarningController::class, 'edit'])
                    ->middleware('permission:edit warnings')
                    ->name('edit');
                Route::put('/{id}', [WarningController::class, 'update'])
                    ->middleware('permission:edit warnings')
                    ->name('update');
                Route::delete('/{id}', [WarningController::class, 'destroy'])
                    ->middleware('permission:delete warnings')
                    ->name('destroy');
                Route::get('/employee/{employeeId}', [WarningController::class, 'employeeWarnings'])
                    ->middleware('permission:view warnings')
                    ->name('employee');
            });
            /* ================== Vacations ROUTES ================== */
            Route::prefix('vacations')->name('vacations.')->group(function () {
                Route::get('/', [VacationController::class, 'index'])
                    ->middleware('permission:view vacations')
                    ->name('index');
                Route::get('/create', [VacationController::class, 'create'])
                    ->middleware('permission:create vacations')
                    ->name('create');
                Route::post('/', [VacationController::class, 'store'])
                    ->middleware('permission:create vacations')
                    ->name('store');
                Route::get('/{id}', [VacationController::class, 'show'])
                    ->middleware('permission:view vacations')
                    ->name('show');
                Route::get('/{id}/edit', [VacationController::class, 'edit'])
                    ->middleware('permission:edit vacations')
                    ->name('edit');
                Route::put('/{id}', [VacationController::class, 'update'])
                    ->middleware('permission:edit vacations')
                    ->name('update');
                Route::delete('/{id}', [VacationController::class, 'destroy'])
                    ->middleware('permission:delete vacations')
                    ->name('destroy');
            });
            Route::prefix('vacation-types')->name('vacation-types.')->group(function () {
                Route::get('/', [VacationTypeController::class, 'index'])
                    ->middleware('permission:view vacations')
                    ->name('index');
                Route::get('/create', [VacationTypeController::class, 'create'])
                    ->middleware('permission:create vacations')
                    ->name('create');
                Route::post('/', [VacationTypeController::class, 'store'])
                    ->middleware('permission:create vacations')
                    ->name('store');
                Route::get('/{id}', [VacationTypeController::class, 'show'])
                    ->middleware('permission:view vacations')
                    ->name('show');
                Route::get('/{id}/edit', [VacationTypeController::class, 'edit'])
                    ->middleware('permission:edit vacations')
                    ->name('edit');
                Route::put('/{id}', [VacationTypeController::class, 'update'])
                    ->middleware('permission:edit vacations')
                    ->name('update');
                Route::delete('/{id}', [VacationTypeController::class, 'destroy'])
                    ->middleware('permission:delete vacations')
                    ->name('destroy');
            });
            Route::prefix('vacation-balances')->name('vacation-balances.')->group(function () {
                Route::get('/', [EmployeeVacationBalanceController::class, 'index'])
                    ->middleware('permission:view vacations')
                    ->name('index');
                Route::get('/create', [EmployeeVacationBalanceController::class, 'create'])
                    ->middleware('permission:create vacations')
                    ->name('create');
                Route::post('/', [EmployeeVacationBalanceController::class, 'store'])
                    ->middleware('permission:create vacations')
                    ->name('store');
                Route::get('/{id}', [EmployeeVacationBalanceController::class, 'show'])
                    ->middleware('permission:view vacations')
                    ->name('show');
                Route::get('/{id}/edit', [EmployeeVacationBalanceController::class, 'edit'])
                    ->middleware('permission:edit vacations')
                    ->name('edit');
                Route::put('/{id}', [EmployeeVacationBalanceController::class, 'update'])
                    ->middleware('permission:edit vacations')
                    ->name('update');
                Route::delete('/{id}', [EmployeeVacationBalanceController::class, 'destroy'])
                    ->middleware('permission:delete vacations')
                    ->name('destroy');
                // Bulk operations
                Route::get('/bulk/create', [EmployeeVacationBalanceController::class, 'bulkCreate'])
                    ->middleware('permission:create vacations')
                    ->name('bulk.create');
                Route::post('/bulk/store', [EmployeeVacationBalanceController::class, 'bulkStore'])
                    ->middleware('permission:create vacations')
                    ->name('bulk.store');
            });

            Route::get('/holidays', [HolidayController::class, 'index'])
                ->middleware('permission:create vacations')
                ->name('holidays.index');
            Route::get('/holidays/create', [HolidayController::class, 'create'])
                ->middleware('permission:create vacations')
                ->name('holidays.create');
            Route::post('/holidays', [HolidayController::class, 'store'])
                ->middleware('permission:create vacations')
                ->name('holidays.store');
            Route::get('/holidays/{holiday}', [HolidayController::class, 'show'])
                ->middleware('permission:create vacations')
                ->name('holidays.show');
            Route::get('/holidays/{holiday}/edit', [HolidayController::class, 'edit'])
                ->middleware('permission:create vacations')
                ->name('holidays.edit');
            Route::put('/holidays/{holiday}', [HolidayController::class, 'update'])
                ->middleware('permission:create vacations')
                ->name('holidays.update');
            Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])
                ->middleware('permission:delete vacations')
                ->name('holidays.destroy');
        });
    });
});
Route::get('/auth/email/verify', [RegisterController::class, 'email_verify'])->name('auth.email-verify');
Route::get('/', [HomeController::class, 'index'])->name('new-home');
Route::post('/start-work', [TimeSheetController::class, 'startWork'])->name('start.work');
Route::post('/stop-work', [TimeSheetController::class, 'stopWork'])->name('stop.work');
Route::get('/get-time-logs', [TimeSheetController::class, 'getTimeLogs'])->name('get.time.logs');
Route::post('/verify-pin', [TimeSheetController::class, 'verifyPin'])->name('verify.pin');
Route::post('/store-temp-file', [HomeController::class, 'storeTempFile'])->name('storeTempFile');
Route::post('/delete-temp-file', [HomeController::class, 'deleteTempFile'])->name('deleteTempFile');
Route::get('{any}', [HomeController::class, 'index'])->name('index');

Route::get('/vacation-request/index', [VacationRequestController::class, 'index'])->name('vacation-request.index');
// New routes for form submission and API
Route::post('/vacation-request/store', [VacationRequestController::class, 'store'])->name('vacation-request.store');
Route::get('/api/vacation-balance/{employeeId}/{vacationTypeId}', [VacationRequestController::class, 'getVacationBalance']);
