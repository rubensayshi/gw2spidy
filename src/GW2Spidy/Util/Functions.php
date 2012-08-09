<?php

namespace GW2Spidy\Util;

abstract class Functions {
    public static function almostEqualCompare($left, $right) {
        $p = 0;
        $i = similar_text($left, $right, $p);

        return $p;
    }
}

?>