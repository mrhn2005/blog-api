<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostTagController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('tags', TagController::class);

    Route::controller(PostTagController::class)->prefix('posts')->group(function () {
        Route::put('{post}/tags/attach', 'attach');
        Route::put('{post}/tags/detach', 'detach');
    });
});
