<?php
use oangia\web\Route;
use oangia\web\ApiController;
// this is sus web to inject my package to origin php web app
if (defined('USE_SUS') && ! USE_SUS) return false;

// add sus-config
if (! file_exists(getcwd() . '/sus-config.php')) {
    file_put_contents(getcwd() . '/sus-config.php', file_get_contents(__DIR__ . '/sus-config.php'));
}
require_once getcwd() . '/sus-config.php';

Route::group(['namespace' => '/api/sus/v1', 'type' => 'api'], function () {
    Route::get('/{table}/{id}',     [ApiController::class, 'show']);
    Route::get('/{table}',          [ApiController::class, 'index']);
    Route::post('/{table}',         [ApiController::class, 'store']);
    Route::put('/{table}/{id}',     [ApiController::class, 'update']);
    Route::delete('/{table}/{id}',  [ApiController::class, 'destroy']);
});
