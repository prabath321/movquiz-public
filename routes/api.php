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

/*
| Retrieves movie data from TMDB each day, creates home images, and stores
| them in the database so the mobile app can retrieve them.
| This route should be called daily by a cron job at 00:00.
*/
Route::get('/movie/home', [HomeController::class, 'home']);

/*
| Retrieves the movie home images from the database.
*/
Route::get('/movie/home1', [HomeController::class, 'home1']);

/*
| Retrieves data from TMDB each day, creates questions and levels (series),
| and stores them in the database so the mobile app can retrieve them.
| This route should be called daily by a cron job at 00:00.
|
| Example:
| http://127.0.0.1:8001/api/movie/questions/movie/upcoming/title/1
|
| Parameters:
| keyword  = movie or tv
| keyword1 = upcoming, top_rated, popular, now_playing for movies
|            airing_today, on_the_air, popular, top_rated for TV series
| keyword2 = title or original_name
| series   = 1, 2, 3, 4, 5, or 6
*/
Route::get('/movie/questions/{keyword}/{keyword1}/{keyword2}/{series}', [UpcomingController::class, 'upcoming']);

/*
| Retrieves TV series data from TMDB each day, creates home images, and
| stores them in the database so the mobile app can retrieve them.
| This route should be called daily by a cron job at 00:00.
*/
Route::get('/tvseries/home', [TvController::class, 'home']);

/*
| Retrieves the levels from the database.
*/
Route::get('/level/{newmodel}', [DataController::class, 'level']);

/*
| Retrieves all questions from the database. This route is the core of the
| data retrieval process.
|
| Example:
| http://127.0.0.1:8001/api/data/MovieUpcoming/1/
|
| Parameters:
| model  = MovieUpcoming, MovieTopRated, MoviePopular, MovieNowPlaying,
|          TvAiringToday, TvOnTheAir, TvPopular, or TvTopRated
| series = 1, 2, 3, 4, 5, or 6
*/
Route::get('/data/{model}/{series}', [DataController::class, 'index']);

/*
| TO DO
| Runs the daily cron process that retrieves data from TMDB, creates
| questions and levels (series), and prepares the home page data.
*/
Route::get('/cron/{secret}', [CronController::class, 'index']);
