<?php

namespace GW2Spidy\Util;

abstract class Functions {
    public static function almostEqualCompare($left, $right) {
        if (trim(strtolower($left)) == trim(strtolower($right))) {
            return 100;
        }

        $p = 0;
        $i = similar_text($left, $right, $p);

        return $p;
    }
}

?>