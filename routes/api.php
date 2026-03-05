<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LockerController;

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        // Por Rol
        Route::middleware('role:super-admin')->group(function () {
            Route::post('/lockers', [LockerController::class, 'store']);
        });

        // Por permiso
        Route::middleware('permission:view-locker')->group(function () {
            Route::post('/lockers', [LockerController::class, 'store']);
            Route::get('/lockers/{locker}', [LockerController::class, 'show'])->whereNumber('locker');
            Route::get('/lockers', [LockerController::class, 'index']);
            Route::put('/lockers/{id}', [LockerController::class, 'update']);
        });

    });