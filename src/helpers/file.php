<?php
function save_file($file, $content, $mode = 0777) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, $mode, true);
    }
    file_put_contents($file, $content);
}

function get_file($file, $default = '') {
    $content = @file_get_contents($file);
    if (! $content) {
        return $default;
    }
    return $content;
}
