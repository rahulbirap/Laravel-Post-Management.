<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Welcome page route
Route::get('/', function () {
    return view('welcome');
});

// Guest middleware routes for authentication
Route::group(['middleware' => 'guest'], function() {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::get('/register', [AuthController::class, 'register_view'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Auth middleware routes for authenticated users
Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', [PostController::class, 'home'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // Post management routes
    Route::post('/posts', [PostController::class, 'save_post'])->name('savepost');
    Route::get('/edit_post/{post}',[PostController::class ,'get_post']);
    Route::delete('/delete_post/{id}',[PostController::class ,'delete_post']);
    Route::post('/update_post/{id}',[PostController::class ,'update_post']);

});
