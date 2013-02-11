<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Util\Functions;

class GenericHelpersExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'round' => new \Twig_Filter_Method($this, 'round'),
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'floor' => new \Twig_Filter_Method($this, 'floor'),
            'rarity_css_class' => new \Twig_Filter_Method($this, 'rarity_css_class'),
            'slugify' => new \Twig_Filter_Method($this, 'slugify'),
            'clean_whitespace' => new \Twig_Filter_Method($this, 'clean_whitespace'),
            'karma' => new \Twig_Filter_Method($this, 'karma',  array('is_safe' => array('html'))),
        );
    }
    public function getFunctions() {
        return array(
            'gw2db' => new \Twig_Function_Method($this, 'gw2db_item'),
            'gw2db_item' => new \Twig_Function_Method($this, 'gw2db_item'),
            'gw2db_recipe' => new \Twig_Function_Method($this, 'gw2db_recipe'),
            'now' => new \Twig_Function_Method($this, 'now'),
        );
    }

    public function gw2db_item($item) {
        return Functions::getGW2DBLink($item);
    }

    public function gw2db_recipe($recipe) {
        return Functions::getGW2DBLinkRecipe($recipe);
    }

    public function slugify($str) {
        return Functions::slugify($str);
    }

    public function floor($num) {
        return floor($num);
    }

    public function round($num) {
        return round($num);
    }

    public function ceil($num) {
        return ceil($num);
    }

    public function now($format = "Y-m-d H:i:s") {
        return date($format);
    }

    public function rarity_css_class($rarity) {
        return strtolower("rarity-" . str_replace(" ", "-", $rarity));
    }

    public function clean_whitespace($str) {
        return trim(preg_replace('/\n /', "\n", preg_replace('/ +/', ' ', $str)));
    }

    public function karma($karma) {
        return number_format($karma) . ' <img alt="Karma" src="/assets/img/Karma.png" height="15" width="18">';
    }

    public function getName() {
        return 'generic_helpers';
    }
}
