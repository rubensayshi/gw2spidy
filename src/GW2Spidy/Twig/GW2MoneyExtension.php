<?php

namespace GW2Spidy\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class GW2MoneyExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            'gw2money' => new \Twig_Filter_Method($this, 'getGW2Money'),
        );
    }

    public function getGW2Money($copper) {
        $copper   = intval($copper);
        if ($negative = $copper < 0) {
            $copper *= -1;
        }

        $result = "";

        if ($gold = floor($copper / 10000)) {
            $copper = $copper % ($gold * 10000);

            if ($negative) {
                $gold *= -1;
            }

            $result .= "{$gold}g ";
        }

        if ($silver = floor($copper / 100)) {
            $copper = $copper % ($silver * 100);

            if ($negative) {
                $silver *= -1;
            }

            $result .= "{$silver}s ";
        }

        if ($copper) {
            if ($negative) {
                $copper *= -1;
            }

            $result .= "{$copper}c ";
        }

        return $result ? trim($result) : '0c';
    }

    public function getName() {
        return 'gw2money';
    }
}
