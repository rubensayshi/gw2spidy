<?php

namespace GW2Spidy\Twig;

class GenericHelpersExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'floor' => new \Twig_Filter_Method($this, 'floor'),
            'rarity_css_class' => new \Twig_Filter_Method($this, 'rarity_css_class'),
        );
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
