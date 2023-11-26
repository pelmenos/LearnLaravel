<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\PermissionController;

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


Route::prefix('files')->middleware('auth:sanctum')->group(function () {
    Route::post('/{file:file_id}/accesses', [PermissionController::class, 'add'])->can('accessesAdd', 'file');;
    Route::delete('/{file:file_id}/accesses', [PermissionController::class, 'delete'])->can('accessesDelete', 'file');;

    Route::get('/disk', [FileController::class, 'userFiles']);
    Route::get('/shared', [FileController::class, 'userAccessFiles']);

    Route::post('/', [FileController::class, 'store']);
    Route::patch('/{file:file_id}', [FileController::class, 'edit'])->can('update', 'file');;
    Route::delete('/{file:file_id}', [FileController::class, 'delete'])->can('destroy', 'file');
    Route::get('/{file:file_id}', [FileController::class, 'download'])->can('view', 'file');;
});
