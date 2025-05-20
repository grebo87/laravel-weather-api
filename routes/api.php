<?php

use App\Http\Controllers\Api\V1\FavoriteCityController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\UserSearchHistoryController;
use App\Http\Controllers\Api\V1\WeatherController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(static function () {
        Route::post('register', RegisterController::class)
            ->name('api.auth.register');

        Route::post('login', LoginController::class)
            ->name('api.auth.login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', LogoutController::class)
            ->name('api.auth.logout');

        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        })->name('api.user');

        Route::get('weather', WeatherController::class)
            ->name('api.weather.get');

        Route::get('/favorites', [FavoriteCityController::class, 'index'])
            ->name('api.favorites.index');
        Route::post('/favorites', [FavoriteCityController::class, 'store'])
            ->name('api.favorites.store');
        Route::delete('/favorites/{favoriteCity}', [FavoriteCityController::class, 'destroy'])
            ->name('api.favorites.destroy');

        Route::get('/history', UserSearchHistoryController::class)
            ->name('api.history.index');
    });
});
