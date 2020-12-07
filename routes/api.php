<?php

use App\Http\Controllers\CategoryMagazineController;
use App\Http\Controllers\CategoryTopicController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api', 'cors']], function () {
    Route::get('user/profile', [UserController::class, 'showAuth']);
    Route::post('user/{id}', [UserController::class, 'update']);

    Route::post('publisher', [PublisherController::class, 'store']);
    Route::post('publisher/{id}', [PublisherController::class, 'update']);
    Route::post('publisher/upload/{id}', [PublisherController::class, 'upload']);
    Route::post('q/publisher', [PublisherController::class, 'search']);
    Route::get('user/req/publisher/{id}', [PublisherController::class, 'reqPublish']);
    Route::get('user/cencel/req/publisher/{id}', [PublisherController::class, 'cancelReqPublish']);
    Route::post('user/magazine/{id}/rating', [MagazineController::class, 'ratingMagazine']);
    Route::delete('magazine/{id}/delete/rating', [MagazineController::class, 'daleteRating']);
    Route::get('publisher/teammate', [PublisherController::class, 'getTeammate']);
});

Route::group(['middleware' => ['cors']], function() {
    Route::get('user/{token}/token', [UserController::class, 'showUser']);
    Route::post('signin', [UserController::class, 'signin']);
    Route::post('signup', [UserController::class, 'signup']);
    Route::post('upload/avatar/{id}', [UserController::class, 'upload']);
    Route::post('send/verify', [UserController::class, 'sendVerify']);
    Route::post('verify/{token}/token', [UserController::class, 'verify']);

    Route::resource('category/magazine', CategoryMagazineController::class);
    Route::post('category/magazine/q', [CategoryMagazineController::class, 'search']);
    Route::resource('category/topic', CategoryTopicController::class);
    Route::post('category/topic/q', [CategoryTopicController::class, 'search']);
    Route::resource('magazine', MagazineController::class);
    Route::delete('magazine/{id}/delete', [MagazineController::class, 'softDestroy']);
    Route::post('q/magazine', [MagazineController::class, 'search']);
    Route::post('q/magazine/{id}/topic', [MagazineController::class, 'searchTopic']);
    Route::post('download/magazine/{id}', [MagazineController::class, 'downloadAssets']);
    Route::get('deleted/magazine', [MagazineController::class, 'deleted']);
    Route::get('recover/magazine/{id}', [MagazineController::class, 'recover']);
    Route::get('popular/magazine', [MagazineController::class, 'mostPopular']);
    Route::post('sort/magazine', [MagazineController::class, 'sortedBy']);
    Route::resource('topic', TopicController::class);
    Route::get('magazine/{magazineId}/topic/category/{id}', [TopicController::class, 'getTopicByCategory']);
    Route::get('deleted/topic', [TopicController::class, 'deleted']);
    Route::get('recover/topic/{id}', [TopicController::class, 'recover']);
    Route::delete('topic/{id}/delete', [TopicController::class, 'softDestroy']);
    Route::delete('/file/topic/{id}/delete', [TopicController::class, 'deleteFiles']);
    Route::post('topic/q', [TopicController::class, 'search']);

});
