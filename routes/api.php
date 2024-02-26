<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

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
Route::middleware("auth:sanctum")->group(function ()
{
    Route::get('/show', [ApiController::class, "show"]);
    Route::post('/store', [ApiController::class, "store"]);
    Route::put('/update', [ApiController::class, "update"]);
    Route::delete('/delete', [ApiController::class, "destroy"]);
});

Route::post('/createaccount', [ApiController::class, "createaccount"]);
Route::delete('/deleteaccount', [ApiController::class, "deleteaccount"]);
Route::get('/showuser', [ApiController::class, "showUser"]);

