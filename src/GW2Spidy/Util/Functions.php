<?php

namespace GW2Spidy\Util;

abstract class Functions {
    public static function almostEqualCompare($left, $right) {
        return static::cleanUpStringForCompare($left) == static::cleanUpStringForCompare($right);
    }

    public static function cleanUpStringForCompare($string) {
        $string = strtolower($string);
        $string = str_replace(" ", "", $string);
        $string = str_replace("'s", "", $string);

        $synonyms = array(
            'crawfish' => 'crawdad',
            'rough'    => 'copper',
        );

        $string = str_replace(array_keys($synonyms), array_values($synonyms), $string);

        return $string;
    }
}

?>