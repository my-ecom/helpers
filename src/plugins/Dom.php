<?php
namespace oangia\plugins;

use oangia\plugins\css\Selector;
use oangia\plugins\css\Media;

class Dom {
    public $name;
    public $fulltag;
    public $path;
    public $id;
    public $classes;
    public $content;
    public $paths = ['Wrapper'];
    public $_children = [];

    function __construct($name, $content = '') {
        $this->fulltag = $name;
        $name = explode(' ', $name);
        $this->name = $name[0];
        $this->content = $content;
        $this->id = $this->getAttr('id');
        $this->classes = array_filter(explode(' ', $this->getAttr('class')));
        $this->path = $this->getPath();
    }

    public function getPath() {
        $path = $this->name;
        if ($this->id) {
            $path .= '#' . $this->id;
        }
        foreach ($this->classes as $class) {
            $path .= '.' . $class;
        }
        return $path;
    }

    public function setParent($dom) {
        $this->paths = explode('>', $dom);
    }

    public function checkEndDom($name) {
        if ($name == '/' . $this->name) {
            $this->endDom = true;
        }
    }

    public function children($doms) {
        $single = ['Wrapper', '!DOCTYPE', 'meta', 'link', 'img', 'input', 'hr', 'br'];
        $curKey = 0;
        foreach ($doms as $key => $item) {
            if ($key < $curKey) continue;
            if ($item[0] == '/' . $this->name) {
                return $key;
            }
            $dom = new Dom($item[0], $item[1]);
            if (! in_array($dom->name, $single)) {
                $end = $dom->children(array_slice($doms, $key + 1));
                $curKey = $key + 2 + $end;
            }
            $this->_children[] = $dom;
        }
    }

    public function exportHtml() {
        $single = ['Wrapper', '!DOCTYPE', 'meta', 'link', 'img', 'input', 'hr'];
        $html = '';
        if ($this->name != 'Wrapper') {
            $html .= '<' . $this->fulltag . '>';
        }
        if ($this->content) {
            $html .= $this->content;
        }
        foreach ($this->_children as $dom) {
            $html .= $dom->exportHtml();
        }
        if (! in_array($this->name, $single)) {
            $html .= '</' . $this->name .'>';
        }
        return $html;
    }

    public function getAttr($attr) {
        return get_string_between($this->fulltag, $attr . '="', '"');
    }

    public function isAttr($attr) {
        if (strpos($this->fulltag, $attr) !== false) return true;
        return false;
    }

    public function getCss() {
        $css = '';
        if ($this->name == 'link') {
            $rel = $this->getAttr('rel');
            if ($rel == 'stylesheet') {
                $cssFile = $this->getAttr('href');
                $css .= file_get_contents($cssFile);
            }
        }
        foreach ($this->_children as $dom) {
            $css .= $dom->getCss();
        }
        return $css;
    }

    public function searchChild($child, $first = true) {
        $curCheck = $this->checkSingleSelector($child[0]);
        if ($curCheck) {
            if (count($child) == 1) return true;
            array_shift($child);
            foreach ($this->_children as $dom) {
                $isUse = $dom->searchChild($child, false);
                if ($isUse) return true;
            }
        }
        if (! $first) return false;
        foreach ($this->_children as $dom) {
            $isUse = $dom->searchChild($child);
            if ($isUse) return true;
        }
        return false;
    }

    public function searchDescendant($descendant) {
        if ($this->checkSingleSelector($descendant[0])) {
            if (count($descendant) == 1) return true;
            array_shift($descendant);
        }
        foreach ($this->_children as $dom) {
            if ($dom->searchDescendant($descendant)) return true;
        }
        return false;
    }

    public function searchAdjacent($adjacent) {
        foreach ($this->_children as $key => $dom) {
            if ($dom->checkSingleSelector($adjacent[0])) {
                if (isset($this->_children[$key + 1]) && $this->_children[$key + 1]->checkSingleSelector($adjacent[1])) {
                    return true;
                }
            }
            if ($dom->searchAdjacent($adjacent)) return true;
        }
        return false;
    }

    public function isCritical($selector) {
        foreach ($selector->selectors as $cSelector) {
            if ($cSelector == '*') return true;
            if ($cSelector == ':root') return true;
            if ($this->checkSingleSelector($cSelector)) return true;
            if (strpos($cSelector, ' ') !== false) {
                $descendant = explode(' ', $cSelector);
                if ($this->searchDescendant($descendant)) return true;
            }
            if (strpos($cSelector, '>') !== false) {
                $child = explode('>', $cSelector);
                if ($this->searchChild($child)) return true;
            }
            if (strpos($cSelector, '+') !== false) {
                $adjacent = explode('+', $cSelector);
                if ($this->searchAdjacent($adjacent)) return true;
            }
        }
        foreach ($this->_children as $dom) {
            if ($dom->isCritical($selector)) return true;
        }
        return false;
    }

    public function isUse($selector) {
        foreach ($selector->selectors as $cSelector) {
            if ($this->search($cSelector)) return true;
        }
        return false;
    }

    public function removeUncritical($fulltag) {
        global $criticalDone;
        if ($this->fulltag == $fulltag) {
            $criticalDone = true;
        }
        if (isset($criticalDone) && $criticalDone) {
            return true;
        }
        foreach ($this->_children as $key => $dom) {
            if ($dom->removeUncritical($fulltag)) {
                unset($this->_children[$key]);
            }
        }
        return false;
    }

    private function removePostFix($selector) {
        $selector = explode(':', $selector);
        return $selector[0];
    }

    public function checkSingleSelector($selector) {
        if ($selector == '*') return true;
        if ($selector == $this->name) return true;
        if ($selector == $this->name . '::after') return true;
        if ($selector == $this->name . '::before') return true;
        if ($selector == $this->name . ':after') return true;
        if ($selector == $this->name . ':before') return true;
        if ($selector == $this->id) return true;
        if ($selector == $this->id . '::after') return true;
        if ($selector == $this->id . '::before') return true;
        if ($selector == $this->id . ':after') return true;
        if ($selector == $this->id . ':before') return true;
        foreach ($this->classes as $class) {
            if ($selector == '.' . $class) return true;
            if ($selector == '.' . $class . '::after') return true;
            if ($selector == '.' . $class . '::before') return true;
            if ($selector == '.' . $class . ':after') return true;
            if ($selector == '.' . $class . ':before') return true;
        }
        return false;
    }

    public function search($selector) {
        if ($selector == ':root') return true;
        if ($selector == '*') return true;
        $adjacentSearch = strpos($selector, '+');
        if ($adjacentSearch !== false) {
            $selectors = explode('+', $selector);
            foreach ($this->_children as $key => $dom) {
                if ($dom->isSelector($selectors[0])) {
                    if (! isset($this->_children[$key])) continue;
                    if ($this->_children[$key]->isSelector($selectors[1])) return true;
                }
            }
        }
        $siblingSearch = strpos($selector, '~');
        if ($siblingSearch !== false) {
            $selectors = explode('~', $selector);
            if ($this->search($selectors[0]) && $this->search($selectors[1])) return true;
        }
        $childSearch = strpos($selector, '>');
        if ($childSearch !== false) {
            $selectors = explode('>', $selector);
            if ($this->isSelector($selectors[0])) {
                unset($selectors[0]);
                foreach ($this->_children as $dom) {
                    if ($dom->search(implode('>', $selectors))) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        $spaceSearch = strpos($selector, ' ');
        if ($spaceSearch !== false) {
            $selectors = explode(' ', $selector);
            if ($this->isSelector($selectors[0])) {
                unset($selectors[0]);
                foreach ($this->_children as $dom) {
                    if ($dom->search(implode(' ', $selectors))) return true;
                }
            }
        }
        if ($this->isSelector($selector)) return true;
        foreach ($this->_children as $childDom) {
            if ($childDom->search($selector)) return true;
        }
        return false;
    }

    public function searchParent($selector) {
        if (! isset($this->parent)) return false;
        if ($this->parent->isSelector($selector)) return true;
        $spaceSearch = strpos($selector, ' ');
        if ($spaceSearch !== false) {
            $selectors = explode(' ', $selector);
            $search = array_pop($selectors);
            if ($this->parent->isSelector($search)) {
                if (empty($selectors)) return true;
                if ($this->parent->searchParent(implode(' ', $selectors))) return true;
            }
        }
        if ($this->parent->searchParent($selector)) return true;
        return false;
    }

    public function isSelector($selector) {
        if ($selector == '*') return true;
        if ($selector == $this->name) return true;
        if (strpos($selector, ':') === 0) return true;
        if (strpos($selector, $this->name . ':') === 0) return true;

        if ($this->id) {
            if ($selector == '#' . $this->id) return true;
            if (strpos($selector, '#' . $this->id . ':') === 0) return true;
        }
        foreach ($this->classes as $class) {
            if ($selector == '.' . $class) return true;
            if (strpos($selector, '.' . $class . ':') === 0) return true;
        }
        return false;
    }
}
