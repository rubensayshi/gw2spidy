<?php

namespace GW2Spidy\Twig;

class GenericHelpersExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'floor' => new \Twig_Filter_Method($this, 'floor'),
        );
    }

    public function floor($num) {
        return floor($num);
    }

    public function ceil($num) {
        return ceil($num);
    }

    public function getName() {
        return 'generic_helpers';
    }
}
