<?php


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    SuperAdminController,
    ProfileController,
    AdminsController,
    RolesController,
    DepartmentController,
    EmployeeController,
    PositionsController,
    TimeSheetController,
    VacationController
};
use App\Http\Controllers\Admin\Employees\{
    HourRateController,
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
            /* ================== Hour_Rate ROUTES ================== */
            Route::prefix('hour_rate')->name('hour_rate.')->group(function () {
                Route::get('/', [HourRateController::class, 'index'])
                    ->middleware('permission:view hour_rate')
                    ->name('index');
                Route::get('/create', [HourRateController::class, 'create'])
                    ->middleware('permission:create hour_rate')
                    ->name('create');
                Route::post('/', [HourRateController::class, 'store'])
                    ->middleware('permission:create hour_rate')
                    ->name('store');
                Route::get('/{id}/edit', [HourRateController::class, 'edit'])
                    ->middleware('permission:edit hour_rate')
                    ->name('edit');
                Route::put('/{id}', [HourRateController::class, 'update'])
                    ->middleware('permission:edit hour_rate')
                    ->name('update');
                Route::delete('/{id}/delete', [HourRateController::class, 'destroy'])
                    ->middleware('permission:delete hour_rate')
                    ->name('destroy');
            });
            /* ================== Salaries ROUTES ================== */
            Route::prefix('salaries')->name('salary.')->group(function () {
                Route::get('/', [SalaryController::class, 'index'])
                    ->middleware('permission:view salaries')
                    ->name('index');
                Route::post('/update-all', [SalaryController::class, 'updateAllSalaries'])
                    ->middleware('permission:edit salaries')
                    ->name('supdateAll');
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