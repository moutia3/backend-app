<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeletravailRequestController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GlobalSettingController;



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
        Route::get('/users/{id}', [AuthController::class, 'getUserById']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

        Route::middleware('role:admin')->group(function () {
            Route::get('/global-settings', [GlobalSettingController::class, 'index']);
            Route::post('/global-settings', [GlobalSettingController::class, 'store']);
            Route::put('/global-settings/{id}', [GlobalSettingController::class, 'update']);
            Route::delete('/global-settings/{id}', [GlobalSettingController::class, 'destroy']);
        });
    });
    
    Route::middleware('role:manager|admin')->group(function () {
       
Route::put('/teletravail-requests/{id}/status', [TeletravailRequestController::class, 'updateStatus']);
        Route::get('/show-requests', [TeletravailRequestController::class, 'index']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        Route::put('/departments/{id}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
        Route::get('/teletravail-requests/user/{userId}', [TeletravailRequestController::class, 'showRequestsByUser']);
    });
    
    Route::middleware('role:employee|manager|admin')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
Route::put('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/global-settings', [GlobalSettingController::class, 'getSettings']);
        Route::get('/global-settings/check-availability', [GlobalSettingController::class, 'checkAvailability']);
    });

    Route::middleware('role:manager|employee')->group(function () {
        Route::post('/teletravail-requests', [TeletravailRequestController::class, 'submitRequest']);
        Route::put('/teletravail-requests/{id}', [TeletravailRequestController::class, 'updateRequest']);
        Route::get('/teletravail-requests/{id}', [TeletravailRequestController::class, 'showRequest']);
        Route::get('/teletravail-requests', [TeletravailRequestController::class, 'showRequests']);
     
    });
    
});