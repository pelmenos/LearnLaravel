<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Middleware\IsAuthor;
use App\Http\Middleware\IsOwner;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/registration', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('logout', [UserController::class, 'logout']);

Route::middleware(['auth:sanctum', IsOwner::class])->post('/files/{file:file_id}/accesses', [PermissionController::class, 'add']);
Route::middleware(['auth:sanctum', IsOwner::class])->delete('/files/{file:file_id}/accesses', [PermissionController::class, 'delete']);
Route::middleware('auth:sanctum')->get('/files/disk', [PermissionController::class, 'userFiles']);
Route::middleware('auth:sanctum')->get('/files/shared', [PermissionController::class, 'userAccessFiles']);

Route::middleware('auth:sanctum')->post('files', [FileController::class, 'store']);
Route::middleware(['auth:sanctum', IsOwner::class])->patch('files/{file:file_id}', [FileController::class, 'edit']);
Route::middleware(['auth:sanctum', IsOwner::class])->delete('files/{file:file_id}', [FileController::class, 'delete']);
Route::middleware(['auth:sanctum', IsAuthor::class])->get('files/{file:file_id}', [FileController::class, 'download']);

