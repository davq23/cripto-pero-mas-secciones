<?php

namespace utils;

class RouteRules {
    public static function is_natural($val)
    {   
        return is_numeric($val) && (int)$val >= 0;
    }

    public static function is_format($format)
    {
        return $format === 'json' || $format === 'xml';
    }
}
