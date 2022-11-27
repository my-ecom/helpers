<?php
namespace oangia;

class Route {
    function __construct() {
        $this->parseUri();
    }

    public static function get($path, $controller) {

    }

    public static function post($path, $controller) {
        
    }

    public function navigate() {
      global $table_prefix;
      global $db;
      $db = new Database($fetch = 'assoc');
      $controller = new Controller;

      switch ($this->type) {
          case 'product':
              $slug = $this->parseUri[2];
              $controller->product($slug);
              break;
          case 'api':
              $api = $this->parseUri[2];
              $param = $this->parseUri[3];
              $controller->{$api}($param);
              break;
      }
      // end sus
      die();
    }

    private function parseUri() {
        $this->fullUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $this->parseUri = explode('/', $this->fullUri);
        $this->type = $this->parseUri[1];
        if (! $this->type) {
            $this->type = 'home';
        }
    }
}
