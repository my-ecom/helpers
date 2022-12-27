<?php
namespace oangia\plugins\css;

class Selector {
    public $name;
    function __construct($name, $css) {
        $this->name = $name;
        $this->selectors = explode(',', $name);
        $this->css = $css;
    }

    public function getCss() {
        return $this->name . '{' . $this->css . '}';
    }
}
