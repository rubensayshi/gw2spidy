<?php

namespace GW2Spidy\Util;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\Recipe;

abstract class Functions {
    public static function almostEqualCompare($left, $right) {
        // we dont care about casing or trailing space
        if (trim(strtolower($left)) == trim(strtolower($right))) {
            return 100;
        }

        // if they trimmed off an ' of YadaYada' suffix then it's still fine
        $leftquoted = preg_quote($left);
        if (preg_match("/^{$leftquoted} of .+/", $right)) {
            return 99;
        }

        // otherwise use similar_text to identify the %% equal
        $p = 0;
        $i = similar_text($left, $right, $p);

        return $p;
    }

    public static function slugify($str) {
        $str = preg_replace('/^\s+|\s+$/', '', $str);
        $str = strtolower($str);

        $str = str_replace(str_split('ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;'), str_split('aaaaaeeeeeiiiiooooouuuunc------'), $str);

        $str = preg_replace('/[^a-z0-9 -]/', '', $str);
        $str = preg_replace('/\s+/', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = preg_replace('/-+$/', '', $str);

        return $str;
    }

    public static function getGW2DBLink(Item $item) {
        $id   = urlencode($item->getGw2dbExternalId());
        $slug = urlencode(self::slugify($item->getName()));

        return "http://www.gw2db.com/items/{$id}-{$slug}";
    }

    public static function getGW2DBLinkRecipe(Recipe $recipe) {
        $id   = urlencode($recipe->getGw2dbExternalId());
        $slug = urlencode(self::slugify($recipe->getName()));

        return "http://www.gw2db.com/recipes/{$id}-{$slug}";
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     * @return string Indented version of the original JSON string.
     */
    public static function indent($json) {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}

?>