<?php
namespace oangia\web;

use oangia\web\Route;

class App {
    private function __construct() {

    }

    public static function run() {
        App::startSession();
        App::requireConfig();
        App::runRoutes();
    }

    private static function startSession() {
        session_start();
    }

    private static function requireConfig() {
        if (! file_exists($_SERVER['DOCUMENT_ROOT'] . '/../config.php')) {
            save_file($_SERVER['DOCUMENT_ROOT'] . '/../config.php', file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../config-sample.php'));
        }

        require_once($_SERVER['DOCUMENT_ROOT'] . '/../config.php');
    }

    private static function runRoutes() {
        require_once(APP_PATH . '/routes/web.php');
        Route::end();
    }
}
