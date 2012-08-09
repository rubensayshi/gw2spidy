<?php

namespace GW2Spidy\Util;

abstract class Functions {
    public static function almostEqualCompare($left, $right) {
        return static::cleanUpStringForCompare($left) == static::cleanUpStringForCompare($right);
    }

    public static function cleanUpStringForCompare($string) {
        $string = str_replace(" ", "", $string);
        $string = str_replace("'s", "", $string);
        $string = strtolower($string);

        return $string;
    }
}

?>