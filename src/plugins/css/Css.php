<?php
namespace oangia\plugins\css;

class Css {
    public $selectors = [];
    public $media = [];

    function __construct($file, $direct = false) {
        $this->css = $this->minimize($direct?$file:file_get_contents($file));
        $this->getMedia();
        $this->getCss();
        preg_match_all('/(.*?)\[\[(.+?)]]/', $this->css, $matches);
        foreach ($matches[1] as $key => $selector) {
            $content = $this->cssContent[str_replace('css_', '', $matches[2][$key])];
            if (strpos($content, '[[media_') === 0) {
                $this->selectors[] = new Media($selector, $this->media[str_replace(']]', '', str_replace('[[media_', '', $content))]);
            } else {
                $this->selectors[] = new Selector($selector, $content);
            }
        }
        unset($this->css);
        unset($this->media);
        unset($this->cssContent);
    }

    private function minimize($css) {
        $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
        $css = preg_replace('/\s{2,}/', ' ', $css);
        $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
        $css = preg_replace('/;}/', '}', $css);
        return $css;
    }

    private function getMedia() {
        preg_match_all('/\@(.+?)\{(.+?)\}}/', $this->css, $matches);
        foreach ($matches[2] as $key => $item) {
            $this->media[] = $item . '}';
            $this->css = str_replace('{' . $item . '}}', '{[[media_' . $key . ']]}', $this->css);
        }
    }

    private function getCss()
    {
        preg_match_all('/\{(.+?)\}/', $this->css, $matches);
        $this->cssContent = $matches[1];
        foreach ($matches[0] as $key => $item) {
            $this->css = str_replace($item, '[[css_' . $key . ']]', $this->css);
        }
    }

    public function filterCriticalCss($dom, $stop = false) {
        $cssCritical = [];
        $cssUncritical = [];

        foreach ($this->selectors as $selector) {
            if ($selector instanceof Selector) {
                if (! $dom->isCritical($selector, $stop)) {
                    $cssUncritical[] = $selector->getCss();
                } else {
                    $cssCritical[] = $selector->getCss();
                }
            }
            if ($selector instanceof Media) {
                if ($selector->name == '@media(prefers-reduced-motion:reduce)') {
                    $cssUncritical[] = $selector->getCss();
                    continue;
                }
                $mediaUse = [];
                $mediaUnuse = [];
                foreach ($selector->selectors as $cSelector) {
                    if (! $dom->isCritical($cSelector, $stop)) {
                        $mediaUnuse[] = $cSelector->getCss();
                    } else {
                        $mediaUse[] = $cSelector->getCss();
                    }
                }
                if (! empty($mediaUse)) {
                    $cssCritical[] = $selector->name . '{' . implode('', $mediaUse) . '}';
                }
                if (! empty($mediaUnuse)) {
                    $cssUncritical[] = $selector->name . '{' . implode('', $mediaUnuse) . '}';
                }
            }
        }

        return [$cssCritical, $cssUncritical];
    }

    public function filterUnuseCss($dom) {
        $use = [];
        $unuse = [];

        foreach ($this->selectors as $selector) {
            if ($selector instanceof Selector) {
                if ($dom->isUse($selector)) {
                    $use[] = $selector->getCss();
                } else {
                    $unuse[] = $selector->getCss();
                }
            }
            if ($selector instanceof Media) {
                $mediaUse = [];
                $mediaUnuse = [];
                foreach ($selector->selectors as $cSelector) {
                    if ($dom->isUse($cSelector)) {
                        $mediaUse[] = $cSelector->getCss();
                    } else {
                        $mediaUnuse[] = $cSelector->getCss();
                    }
                }
                if (! empty($mediaUse)) {
                    $use[] = $selector->name . '{' . implode('', $mediaUse) . '}';
                }
                if (! empty($mediaUnuse)) {
                    $unuse[] = $selector->name . '{' . implode('', $mediaUnuse) . '}';
                }
            }
        }

        return [$use, $unuse];
    }

    public function filter($selectors) {
        $cssUse = [];
        $cssUnuse = [];

        foreach ($this->selectors as $selector) {
            if (! $selector->isCritical($selectors)) {
                $cssUnuse[] = $selector->getCss();
                continue;
            }
            if ($selector instanceof Selector) {
                $cssUse[] = $selector->getCss();
                continue;
            }
            if ($selector instanceof Media) {
                $mediaCssUse = [];
                $mediaCssUnuse = [];
                foreach ($selector->selectors as $cSelector) {
                    if ($cSelector->isCritical($selectors)) {
                        $mediaCssUse[] = $cSelector->getCss();
                    } else {
                        $mediaCssUnuse[] = $cSelector->getCss();
                    }
                }
                if (!empty($mediaCssUse)) {
                    $cssUse[] = $selector->name . '{' . implode('', $mediaCssUse) . '}';
                }
                if (!empty($mediaCssUnuse)) {
                    $cssUnuse[] = $selector->name . '{' . implode('', $mediaCssUnuse) . '}';
                }
            }
        }
        return [$cssUse, $cssUnuse];
    }
}
