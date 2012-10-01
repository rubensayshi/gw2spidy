<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Util\Functions;

class GenericHelpersExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'floor' => new \Twig_Filter_Method($this, 'floor'),
            'rarity_css_class' => new \Twig_Filter_Method($this, 'rarity_css_class'),
            'slugify' => new \Twig_Filter_Method($this, 'slugify'),
        );
    }
    public function getFunctions() {
        return array(
            'gw2db' => new \Twig_Function_Method($this, 'gw2db'),
        );
    }

    public function gw2db($item) {
        return Functions::getGW2DBLink($item);
    }

    public function slugify($str) {
        return Functions::slugify($str);
    }

    public function floor($num) {
        return floor($num);
    }

    public function ceil($num) {
        return ceil($num);
    }

    public function rarity_css_class($rarity) {
        return strtolower("rarity-" . str_replace(" ", "-", $rarity));
    }

    public function getName() {
        return 'generic_helpers';
    }
}
