<?php
namespace oangia\web;

class Route {
    public static $uri = null;
    public static $fullUri = '';
    public static $params = [];
    public static $namespace = '';

    private function __construct() {

    }

    public static function get($path, $controller) {
        if (! Request::is_method('GET')) return;
        Route::run($path, $controller);
    }

    public static function post($path, $controller) {
        if (! Request::is_method('POST')) return;
        Route::run($path, $controller);
    }

    public static function put($path, $controller) {
        if (! Request::is_method('PUT')) return;
        Route::run($path, $controller);
    }

    public static function delete($path, $controller) {
        if (! Request::is_method('DELETE')) return;
        Route::run($path, $controller);
    }

    public static function group($params, $controller) {
        if (! Route::$uri) {
            Route::parseUri();
        }
        $namespace = isset($params['namespace'])?$params['namespace']:'';
        $type = isset($params['type'])?$params['type']:'web';
        if (strpos(Route::$fullUri, $namespace) !== 0) return;
        if ($type == 'api' && ! Request::is_xhr()) return;
        Route::$namespace = $namespace;
        $controller();
        Route::$namespace = '';
    }

    private static function run($path, $controller) {
        if (! Route::uri_match(Route::$namespace . $path)) return;
        Route::runController($controller);
    }

    private static function uri_match($path) {
        if (! Route::$uri) {
            Route::parseUri();
        }
        if ($path == Route::$fullUri) return true;
        $path = array_filter(explode('/', $path));

        if (count($path) != count(Route::$uri)) return false;
        Route::$params = [];
        foreach ($path as $key => $child) {
            $match = null;
            if ($child == Route::$uri[$key] || preg_match('/\{(.*?)\}/', $child, $match)) {
                if ($match) {
                    Route::$params[] = Route::$uri[$key];
                }
                continue;
            }
            return false;
        }
        return true;
    }

    private static function runController($controller) {
        Route::connectDB();
        if (is_array($controller)) {
            $obj = new $controller[0];
            $response = call_user_func_array([$obj, $controller[1]], Route::$params);
        } else {
            $response = call_user_func_array($controller, Route::$params);
        }
        if (is_array($response)) {
            Response::json($response);
        }
        echo $response;
        die();
    }

    private static function connectDB() {
        global $db;
        $db = Database::connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    private static function parseUri() {
        Route::$fullUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        Route::$uri = array_filter(explode('/', Route::$fullUri));
    }
}
