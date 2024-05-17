<?php

use App\Http\Controllers\Api\CronController;
use App\Http\Controllers\Api\DataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Movie\UpcomingController; 
use App\Http\Controllers\Api\Movie\HomeController;
use App\Http\Controllers\Api\Tvseries\TvController;

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

Route::get('/movie/home', [HomeController::class, 'home']);
Route::get('/movie/home1', [HomeController::class, 'home1']);
Route::get('/movie/questions/{keyword}/{keyword1}/{keyword2}/{series}', [UpcomingController::class, 'upcoming']);
Route::get('/tvseries/home', [TvController::class, 'home']);
Route::get('/data/{model}/{series}', [DataController::class, 'index']);
Route::get('/cron/{secret}', [CronController::class, 'index']);

