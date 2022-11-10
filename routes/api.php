<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [UserController::class, 'login']);

Route::post('/register', [UserController::class, 'store']);

Route::post('/logout', [UserController::class, 'logout']);

Route::post('/uploadImage', [ImageController::class, 'uploadImage']);

Route::post('/posts', [PostController::class, 'create']);

Route::delete('/post', [PostController::class, 'delete']);

Route::post('/comment', [PostController::class, 'comment']);

Route::post('/like', [PostController::class, 'like']);
