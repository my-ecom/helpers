<?php
namespace oangia\plugins\css;

class Media {
    public $selectors = [];
    public $name;
    public $notallow = ['@media(prefers-reduced-motion:reduce)', '@media(max-width:499.98px)', '@media(max-width:767.98px)', '@media(max-width:991.98px)'];

    function __construct($name, $css) {
        $this->name = $name;
        preg_match_all('/(.*?)\{(.+?)}/', $css, $matches);
        foreach ($matches[1] as $key => $selector) {
            $this->selectors[] = new Selector($selector, $matches[2][$key]);
        }
    }

    public function getCss() {
        $selectors = '';
        foreach ($this->selectors as $selector) {
            $selectors .= $selector->getCss();
        }
        return $this->name . '{' . $selectors . '}';
    }

    public function isUse($selectors) {
        foreach ($this->notallow as $notallow) {
            if ($this->name == $notallow) {
                return false;
            }
        }

        return true;
    }
}
