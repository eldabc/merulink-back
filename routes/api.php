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

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/login', [AuthController::class, 'login']);
    
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

    // Ruta solo con fin informativo
    Route::get('/check-time', function () {
    return [
        'time_now' => date('Y-m-d H:i:s'),
        'timezone' => date_default_timezone_get(),
    ];
});

    Route::put('employees/{employee}/changeBooleanField', [EmployeeController::class, 'changeStatus']);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('subdepartments', SubDepartmentController::class);
    Route::apiResource('positions', PositionController::class);
    Route::apiResource('events', EventController::class);
    Route::post('events/batch-banking', [EventController::class, 'batchBanking']);
    Route::apiResource('eventCategories', EventCategoryController::class);
    Route::apiResource('eventTemplates', EventTemplateController::class);
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('shifts', ShiftController::class);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        // Por Rol
        Route::middleware('role:super-admin')->group(function () {
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