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
    TimeSheetController
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

            // Route::get('/contact/request', [ContactRequestController::class, 'index'])->name("contact.index");
            Route::prefix('roles')->name('roles.')->group(function () {
                // Display all roles
                Route::get('/', [RolesController::class, 'index'])
                    ->middleware('permission:view roles')
                    ->name('index');

                // Create a new role
                Route::get('/create', [RolesController::class, 'create'])
                    ->middleware('permission:create roles')
                    ->name('create');

                // Store a new role
                Route::post('/', [RolesController::class, 'store'])
                    ->middleware('permission:create roles')
                    ->name('store');

                // Edit an existing role
                Route::get('/{role}/edit', [RolesController::class, 'edit'])
                    ->middleware('permission:edit roles')
                    ->name('edit');

                // Update an existing role
                Route::put('/{role}', [RolesController::class, 'update'])
                    ->middleware('permission:edit roles')
                    ->name('update');

                // Delete a role
                Route::delete('/{role}', [RolesController::class, 'destroy'])
                    ->middleware('permission:delete roles')
                    ->name('destroy');

            });
            // admins Routes
            Route::prefix('admins')->name('admins.')->group(function () {
                Route::get('/', [AdminsController::class, 'index'])
                    ->middleware('permission:view admins')
                    ->name('index');

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

                Route::get('view/{user}', [AdminsController::class, 'show'])
                    ->middleware('permission:view admins')
                    ->name('show');

                Route::post('toggle-ban', [AdminsController::class, 'toggleBan'])
                    ->name('toggle-ban');
            });

            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [EmployeeController::class, 'index'])
                    ->middleware('permission:view employees')
                    ->name('index');
                Route::get('/create', [EmployeeController::class, 'create'])
                    ->middleware('permission:create employees')
                    ->name('create');
                Route::post('/store', [EmployeeController::class, 'store'])
                    ->middleware('permission:create employees')
                    ->name('store');
                Route::get('/employee/{employee}', [EmployeeController::class, 'show'])
                    ->middleware('permission:view employees')
                    ->name('show');
                Route::get('/employee/edit/{employee}', [EmployeeController::class, 'edit'])
                    ->middleware('permission:edit employees')
                    ->name('edit');
                Route::get('/{employee}/departments', [EmployeeController::class, 'show_department'])
                    ->middleware('permission:view employees')
                    ->name('department.index');
                Route::put('/employee/{id}]update', [EmployeeController::class, 'update'])
                    ->middleware('permission:edit employees')
                    ->name('update');
            });
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
            });
            /////////////// TimeSheet Controller routes //////////////////

            Route::get('/employee/{id}/timelogs', [TimeSheetController::class, 'show_employee_timesheet'])
                ->middleware('permission:view employees')
                ->name('employee.timelogs.index');
            
            Route::post('/employee/{id}/timelogs/update', [TimeSheetController::class, 'update'])->name('employee.timelogs.update');
            Route::post('/employee/{id}/timelogs/store', [TimeSheetController::class, 'store'])->name('employee.timelogs.store');
            Route::post('/timesheet/update-all-hours', [TimeSheetController::class, 'updateAllHours'])->name('timesheet.updateAllHours');
            Route::post('/salary/update-all', [SalaryController::class, 'updateAllSalaries'])->name('salary.updateAll');
            /////////////// TimeSheet Controller routes End //////////////////

            /////////////// Shift Controller routes         //////////////////

            Route::get('/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
            Route::get('/shifts/{shift}/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
            Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
            Route::get('/shifts/{shift}/rules', [ShiftController::class, 'showRules'])->name('shifts.rules.show');
            Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
            Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
            Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');

            /////////////// Shift Controller routes End         //////////////////

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
                Route::delete('/delete/{id}', [PositionsController::class, 'delete'])
                    ->middleware('permission:delete positions')
                    ->name('delete');

                // Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('edit');
                // Route::put('/{id}', [DepartmentController::class, 'update'])->name('update');
            });
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
            });

            Route::prefix('salaries')->name('salary.')->group(function () {
                Route::get('/', [SalaryController::class, 'index'])
                    ->middleware('permission:view salaries')
                    ->name('index');

            });
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
            Route::prefix('shifts-rules')->name('shifts-rules.')->group(function () {
                Route::get('/', [ShiftRuleController::class, 'index'])
                    // ->middleware('permission:view companies')
                    ->name('index');
                Route::get('/create', [ShiftRuleController::class, 'create'])->name('create');
                Route::post('/store', [ShiftRuleController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ShiftRuleController::class, 'edit'])->name('edit');
                Route::put('/{id}', [ShiftRuleController::class, 'update'])->name('update');
                

            });
            Route::prefix('warnings')->name('warnings.')->group(function () {
                Route::get('/', [WarningController::class, 'index'])
                    // ->middleware('permission:view companies')
                    ->name('index');
                Route::get('/create', [WarningController::class, 'create'])
                    // ->middleware('permission:view companies')
                    ->name('create');
                Route::get('/show/{id}', [WarningController::class, 'show'])
                    // ->middleware('permission:view companies')
                    ->name('show');
                    Route::post('/', [WarningController::class, 'store'])
                    // ->middleware('permission:view companies')
                    ->name('store');
                Route::get('/{id}/edit', [WarningController::class, 'edit'])
                    // ->middleware('permission:view companies')
                    ->name('edit');
                Route::put('/{id}', [WarningController::class, 'update'])
                    // ->middleware('permission:view companies')
                    ->name('update');
                Route::delete('/{id}', [WarningController::class, 'destroy'])
                    // ->middleware('permission:view companies')
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