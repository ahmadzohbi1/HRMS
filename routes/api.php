<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\NotificationController;
use App\Http\Controllers\Api\VacationActionController;

Route::group(["prefix" => 'v1'], function () {

    
    Route::group(['middleware' => 'auth:sanctum'], function () {

        Route::group(["prefix" => 'notification'], function () {
            Route::get('/list', [NotificationController::class, 'index']);
        });

    });
    Route::get('/clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('optimize');
        Artisan::call('route:cache');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        Artisan::call('l5-swagger:generate');

        $apiKey = "1c425d0e-f5dc-11ee-8906-5ae590c5cc85";
        $secret = "2d105e6af92d80941b0db7a8a5b569ec";
        $hash = hash("sha256", $apiKey . $secret . time());

        return ["success" => true, "data" => $hash];
    });

});

// Vacation action route - secure one-time approve/reject from email
Route::get('/vacation-action/{token}', [VacationActionController::class, 'handleAction'])->name('vacation.action');

