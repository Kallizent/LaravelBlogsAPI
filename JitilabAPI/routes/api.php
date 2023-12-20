<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Definisikan rute CRUD di sini
    Route::get('/blogs', [BlogController::class, 'getAllData']);
    Route::post('/blogs', [BlogController::class, 'InputData']);
    Route::get('/blogs/{id}', [BlogController::class, 'getDataById']);
    Route::post('/blogsUpdate', [BlogController::class, 'UpdateData']);
    Route::delete('/blogs/{id}', [BlogController::class, 'Delete']);
});
