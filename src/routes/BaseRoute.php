<?php
namespace oangia\routes;

use oangia\web\Request;
use oangia\web\Response;

class BaseRoute {
    public static $uri = null;
    public static $fullUri = '';
    public static $params = [];
    public static $namespace = '';

    private function __construct() {}

    public static function get($path, $controller) {
        if (! Request::is_method('GET')) return;
        BaseRoute::run($path, $controller);
    }

    public static function post($path, $controller) {
        if (! Request::is_method('POST')) return;
        BaseRoute::run($path, $controller);
    }

    public static function put($path, $controller) {
        if (Request::is_method('PUT')) BaseRoute::run($path, $controller);
        if (! Request::is_method('POST') || ! isset($_POST['_method']) || $_POST['_method'] != 'PUT') return;
        unset($_POST['_method']);
        BaseRoute::run($path, $controller);
    }

    public static function delete($path, $controller) {
        if (Request::is_method('DELETE')) BaseRoute::run($path, $controller);
        if (! Request::is_method('POST') || ! isset($_POST['_method']) || $_POST['_method'] != 'DELETE') return;
        BaseRoute::run($path, $controller);
    }

    public static function group($params, $controller) {
        if (! BaseRoute::$uri) {
            BaseRoute::parseUri();
        }
        $preNamespace = BaseRoute::$namespace;
        $namespace = $preNamespace . (isset($params['namespace'])?$params['namespace']:'');
        $type = isset($params['type'])?$params['type']:'web';
        if (strpos(BaseRoute::$fullUri, $namespace) !== 0) return;
        if ($type == 'api' && ! Request::is_xhr()) return;
        BaseRoute::$namespace = $namespace;
        $controller();
        BaseRoute::$namespace = $preNamespace;
    }

    public static function domain($domain, $controller) {
        if ($_SERVER['SERVER_NAME'] != $domain) {
            return false;
        }
        $controller();
    }

    public static function redirect($url) {
        header("Location: $url");
        die();
    }

    public static function end() {
        require_once __DIR__ . '/../web/helpers.php';
        BaseRoute::runController(function () {
            return view('404');
        });
    }

    private static function run($path, $controller) {
        if (! BaseRoute::uri_match(BaseRoute::$namespace . $path)) return;
        BaseRoute::runController($controller);
    }

    protected static function runController($controller) {
        if (is_array($controller)) {
            $obj = new $controller[0];
            $response = call_user_func_array([$obj, $controller[1]], BaseRoute::$params);
        } else {
            $response = call_user_func_array($controller, BaseRoute::$params);
        }
        if (is_array($response)) {
            Response::json($response);
        }
        echo $response;
        die();
    }

    private static function uri_match($path) {
        if (! BaseRoute::$uri) {
            BaseRoute::parseUri();
        }
        if ($path == BaseRoute::$fullUri) return true;
        $path = array_filter(explode('/', $path));

        if (count($path) != count(BaseRoute::$uri)) return false;
        BaseRoute::$params = [];
        foreach ($path as $key => $child) {
            $match = null;
            if ($child == BaseRoute::$uri[$key] || preg_match('/\{(.*?)\}/', $child, $match)) {
                if ($match) {
                    BaseRoute::$params[] = BaseRoute::$uri[$key];
                }
                continue;
            }
            return false;
        }
        return true;
    }

    private static function parseUri() {
        BaseRoute::$fullUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        BaseRoute::$uri = array_filter(explode('/', BaseRoute::$fullUri));
    }
}
