<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaptopController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;

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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout',[AuthController::class,'logout']);
    Route::apiResource('/users', UserController::class);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::delete('/products/{productId}/images/{imageId}', [ProductController::class, 'deleteImage']);
    Route::post('/products', [ProductController::class, 'store']);
});

Route::post('/products/{id}', [ProductController::class, 'update']);
Route::post('/signup',[AuthController::class,'signup']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// laptop
Route::post('/laptops',[LaptopController::class, 'store']);
Route::get('/laptops',[LaptopController::class, 'index']);
Route::get('/laptops/{id}', [LaptopController::class, 'show']);
Route::post('/laptops/{id}', [LaptopController::class, 'update']);
Route::post('/update-image', [LaptopController::class, 'updateImageOrder']);