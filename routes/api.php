<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\PadlockPatternController;
use App\Http\Controllers\PadlockController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SubDepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventTemplateController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SchedulePlanningController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\RoleController;

    // Ruta solo con fin informativo
    Route::get('/check-time', function () {
        return [
            'time_now' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
        ];
    });

    Route::post('/login', [AuthController::class, 'login']);

    // Cambio de contraseña
    Route::middleware(['auth:sanctum', 'abilities:password-change'])->group(function () {
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });


    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');
       
        Route::get('/lockers', [LockerController::class, 'index']);
        Route::post('/lockers', [LockerController::class, 'store']);
        Route::get('/lockers/{locker}', [LockerController::class, 'show'])->whereNumber('locker');
        Route::put('/lockers/{locker}', [LockerController::class, 'update'])->whereNumber('locker');
        Route::delete('/lockers/{locker}', [LockerController::class, 'destroy'])->whereNumber('locker');

        Route::group(['prefix' => 'padlocks'], function () {
            Route::apiResource('patterns', PadlockPatternController::class);
            
            Route::apiResource('/', PadlockController::class)->parameters(['' => 'padlock']);
        });
        
        Route::delete('assigns', [AssignController::class, 'destroyByCategory']);
        Route::apiResource('assigns', AssignController::class)->except([
            'update', 'show'
        ]);


        Route::put('employees/{employee}/resetPass', [EmployeeController::class, 'resetPass']);
        Route::put('employees/{employee}/changeBooleanField', [EmployeeController::class, 'changeStatus']);
        Route::get('employees/by-permission', [EmployeeController::class, 'byPermission']);
        Route::apiResource('employees', EmployeeController::class);

        Route::get('/permissions', [RoleController::class, 'allPermissions'])->middleware('role:admin|super-admin');
        Route::get('/roles', [RoleController::class, 'index'])->middleware('role:admin|super-admin');
        Route::get('/roles/permissions', [RoleController::class, 'getRolesPermissions'])->middleware('role:admin|super-admin');

        // Scraper de datos de empleado desde IVSS/SENIAT
        Route::post('/scrape/employee', [ScraperController::class, 'scrapeEmployee']);
        Route::post('/scrape/seniat/captcha', [ScraperController::class, 'getSeniatCaptcha']);

        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('subdepartments', SubDepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('events', EventController::class);
        Route::post('events/batch-banking', [EventController::class, 'batchBanking']);
        Route::apiResource('eventCategories', EventCategoryController::class);
        Route::apiResource('eventTemplates', EventTemplateController::class);
        Route::apiResource('locations', LocationController::class);
        Route::apiResource('shifts', ShiftController::class);
        
        Route::get('/schedule-plannings/filter-schedule', [SchedulePlanningController::class, 'filterSchedule']);
        Route::post('/schedule-plannings/autofill', [SchedulePlanningController::class, 'autofill']);
        Route::post('/schedule-plannings/toggle-autofill', [SchedulePlanningController::class, 'toggleAutofill']);
        Route::apiResource('schedule-plannings', SchedulePlanningController::class);
        Route::apiResource('schedules', ScheduleController::class);
        Route::get('/shifts/next-code/{department_id}', [ShiftController::class, 'getNextCodeData']);

    

        Route::post('/logout', [AuthController::class, 'logout']);
        // Por Rol
        Route::middleware('role:admin|supervisor|employee|guest')->group(function () {
            // Route::post('/lockers', [LockerController::class, 'store']);
        });

        // Por permiso
        // Route::middleware('permission:view-locker')->group(function () {
            // Route::get('/lockers', [LockerController::class, 'index']);
            // Route::post('/lockers', [LockerController::class, 'store']);
            // Route::get('/lockers/{locker}', [LockerController::class, 'show'])->whereNumber('locker');
            // Route::put('/lockers/{locker}', [LockerController::class, 'update'])->whereNumber('locker');
        // });

    });