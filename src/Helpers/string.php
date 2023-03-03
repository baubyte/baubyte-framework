<?php

/**
 * Convert string into snake_case.
 * @param string $str
 * @return string
 */
function snake_case(string $string): string {
    $snake_cased = [];
    $skip = [' ', '-', '_', '/', '\\', '|', ',', '.', ';', ':'];
    $i = 0;
    while ($i < strlen($string)) {
        $last = count($snake_cased) > 0
                ? $snake_cased[count($snake_cased) -1]
                : null;
        $character = $string[$i++];
        if (ctype_upper($character)) {
            if ($last !== '_') {
                $snake_cased[] = '_';
            }
            $snake_cased[] = strtolower($character);
        } elseif (ctype_lower($character)) {
            $snake_cased[] = $character;
        } elseif (in_array($character, $skip)) {
            if ($last !== '_') {
                $snake_cased[] = '_';
            }
            while ($i < strlen($string) && in_array($string[$i], $skip)) {
                $i++;
            }
        }
    }
    if ($snake_cased[0] === '_') {
        $snake_cased[0] = '';
    }
    if ($snake_cased[count($snake_cased)-1] === '_') {
        $snake_cased[count($snake_cased)-1] = '';
    }
    return implode($snake_cased);
}

/**
 * Convert string into camelCase.
 * @param string $str
 * @return string
 */
function camel_case(string $str): string {
    $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
    $str = trim($str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);
    return $str;
}
