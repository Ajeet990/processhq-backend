<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrganizationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get-users', [AuthController::class, 'getAllUsers']);


    // Organization routes
    Route::controller(OrganizationController::class)->group(function () {
        Route::post('/create-organization', 'createOrganization');
    });
    
});
