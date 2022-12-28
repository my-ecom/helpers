<?php
namespace oangia\plugins;

use oangia\plugins\css\Css;

class Html {
    function __construct() {
    }

    public function parseFromUrl($url) {
        return $this->parseFromHtml(file_get_contents($url));
    }

    public function parseFromHtml($html) {
        $this->html = $this->minimize($html);
        return $this->parseDoms();
    }

    private function parseDoms() {
        preg_match_all('/(.+?)>(.*?)</', $this->html, $doms);
        $doms[1][0] = str_replace('<', '', $doms[1][0]);

        $formatDoms = [];
        foreach ($doms[1] as $key => $dom) {
            $formatDoms[$key] = [$dom, $doms[2][$key]];
        }
        $formatDoms[] = ['/html', ''];
        $mainDom = new Dom('Wrapper');
        $mainDom->children($formatDoms);
        return $mainDom;
    }

    private function minimize($html) {
        $html = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $html); // negative look ahead
        $html = preg_replace('/\s{2,}/', ' ', $html);
        $html = preg_replace('/\s*([:;{}])\s*/', '$1', $html);
        $html = preg_replace('/;}/', '}', $html);
        $html = preg_replace('/<!--(.+?)-->/', '', $html);
        $html = trim(preg_replace('/>\s+</', '><', $html));
        return $html;
    }

    public function getCss($mainDom) {
        return new Css($mainDom->getCss(), true);
    }
}
