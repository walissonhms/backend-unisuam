<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GithubController;

Route::group(['prefix' => 'github'], function () {
    Route::get('/username/{username}', [GithubController::class, 'getUserFollowing']);
});