<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);

Route::group(['middleware' => ["auth:sanctum"]], function(){
    Route::get('logout',[UserController::class,'logout']);
    Route::get('user-profile',[UserController::class,'userProfile']);
    Route::get('islogged',[UserController::class,'islogged']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
