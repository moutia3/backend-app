<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeletravailRequestController;



Route::post('/login', action: [AuthController::class, 'login']);

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::get('/reset-password/{token}', function ($token) {
    return redirect()->to("http://localhost:4200/reset-password/$token");
})->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/roles', [AuthController::class, 'getUserRoles']); 
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::delete('/profile', [AuthController::class, 'deleteProfile']);
   
  

    Route::middleware('role:admin')->group(function () {
        Route::post('/addUser', [AuthController::class, 'addUser']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/users', [AuthController::class, 'getAllUsers']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
    });
    
    Route::middleware('role:manager|admin')->group(function () {
        
        Route::put('/posts/{id}', [PostController::class, 'update']);
    });
    
    Route::middleware('role:employee|manager|admin')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
    });

    Route::middleware('role:manager|employee')->group(function () {
        Route::post('/teletravail-requests', [TeletravailRequestController::class, 'submitRequest']);
        Route::put('/teletravail-requests/{id}', [TeletravailRequestController::class, 'updateRequest']);
        Route::get('/teletravail-requests/{id}', [TeletravailRequestController::class, 'showRequest']);
        Route::get('/teletravail-requests', [TeletravailRequestController::class, 'showRequests']);
    });
    
});