<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class GW2MoneyExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'gw2money' => new \Twig_Filter_Method($this, 'getGW2Money'),
        );
    }

    public function getGW2Money($copper) {
        $copper = intval($copper);
        $result = "";

        if ($gold = floor($copper / 10000)) {
            $copper = $copper % ($gold * 10000);
            $result .= "{$gold}g ";
        }

        if ($silver = floor($copper / 100)) {
            $copper = $copper % ($silver * 100);
            $result .= "{$silver}s ";
        }

        if ($copper) {
            $result .= "{$copper}c ";
        }

        return $result ? trim($result) : '0c';
    }

    public function getName() {
        return 'gw2money';
    }
}
