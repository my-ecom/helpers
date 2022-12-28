<?php
function array_unique_recursive($array)
{
    $array = array_unique($array, SORT_REGULAR);

    foreach ($array as $key => $elem) {
        if ( is_array($elem) ) {
            $array[$key] = array_unique_recursive($elem);
        }
    }

    return $array;
}

function array_export($data, $deep=1)
{
    $tab = '    ';
    $text = '';
    if ($deep == 1) {
        $text = '<?php';
        $text .= "\nreturn ";
    }

    if (! is_array($data)) {
        $text .= "'" . $data . "',\n";
    } else {
        $text .= "[\n";
        foreach ($data as $key => $value) {
            for ($i = 0; $i < $deep; ++$i) {
                $text .= $tab;
            }
            if (is_numeric($key)) {
                $text .= $key;
            } else {
                $text .= "'$key'";
            }
            $text .= " => " . array_export($value, $deep + 1);
        }
        $text = rtrim($text, ",\n") . "\n";
        for ($i = 0; $i < $deep - 1; ++$i) {
            $text .= $tab;
        }
        $text .= "],\n";
    }
    if ($deep == 1) {
        return rtrim($text, ",\n") . ";";
    }
    return $text;
}
