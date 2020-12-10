<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/ping',  fn() => ['pong' => true]);

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::post('/user', [AuthController::class, 'create']);
Route::put('/user', [UserController::class, 'update']);
// Route::get('/user', [UserController::class, 'read']);

// Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
// Route::post('/user/cover', [UserController::class, 'updateCover']);
// Route::get('/user/{id}', [UserController::class, 'read']);

// Route::post('/feed', [FeedController::class, 'create']);
// Route::get('/feed', [FeedController::class, 'read']);

// Route::get('/feed/user', [FeedController::class, 'userFeed']);
// Route::get('/feed/user/{id}', [FeedController::class, 'userFeed']);


// Route::post('/post/{id}/like', [PostController::class, 'like']);
// Route::post('/post/{id}/comment', [PostController::class, 'comment']);

// Route::get('/search', [SearchController::class, 'search']);
