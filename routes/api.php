<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TournamentController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['api'], 'namespace' => 'Api\V1', 'prefix' => '/v1'], function (){
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);

    //route group for auth users
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('view-user', [UserController::class, 'view']);
        Route::post('deactivate-user', [UserController::class, 'deactivate']);

        Route::post('create-tournament', [TournamentController::class, 'create']);
        Route::post('invite-friend', [TournamentController::class, 'sendInvite']);
        Route::post('submit-result', [TournamentController::class, 'submitResult']);
        Route::get('show-leaderboard', [TournamentController::class, 'showLeaderBoard']);
        Route::patch('update-result', [TournamentController::class, 'updateLeaderBoard']);
    });
    
});

