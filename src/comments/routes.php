<?php

use oangia\web\Route;

Route::group(['namespace' => '/api/v1', 'type' => 'api'), function() {
    Route::post('/comments',    [CommentController::class, 'store']);
});
