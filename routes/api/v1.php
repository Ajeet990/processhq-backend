<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\ModuleController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::get('/check-token-validity', [AuthController::class, 'checkTokenValidity']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get-users', [AuthController::class, 'getAllUsers']);


    // Organization routes
    Route::controller(OrganizationController::class)->prefix('organization')->group(function () {
        Route::post('/create', 'createOrganization');
        Route::get('/get/{id?}', "getOrganization");
        Route::delete('/delete/{id}', 'deleteOrganization');
    });

    // Module routes
    Route::controller(ModuleController::class)->prefix('module')->group(function () {
        Route::post('/create', 'createModule');
        Route::get('/list', 'getModules');
        // Route::get('/get/{id}', 'getModuleById');
        Route::put('/update/{id}', 'updateModule');
        Route::delete('/delete/{id}', 'deleteModule');
        Route::put('/toggle-status/{id}', 'toggleModuleStatus');
        Route::get('/get-module-by-id/{id}', 'getModuleById');
    });

});
