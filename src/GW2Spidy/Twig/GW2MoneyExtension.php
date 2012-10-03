<?php

namespace GW2Spidy\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class GW2MoneyExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'gw2money' => new \Twig_Filter_Method($this, 'getGW2Money' ,  array('is_safe' => array('html'))),
        );
    }

    public function getGW2Money($copper) {
        $goldImg   = '<i class="gw2money-gold">g</i>';
        $silverImg = '<i class="gw2money-silver">s</i>';
        $copperImg = '<i class="gw2money-copper">c</i>';

        $copper = intval($copper);
        if ($negative = $copper < 0) {
            $copper *= -1;
        }

        $result = "";

        if ($gold = floor($copper / 10000)) {
            $copper = $copper % ($gold * 10000);

            if ($negative) {
                $gold *= -1;
            }

            $result .= "{$gold} {$goldImg} ";
        }

        if ($silver = floor($copper / 100)) {
            $copper = $copper % ($silver * 100);

            if ($negative) {
                $silver *= -1;
            }

            $result .= "{$silver} {$silverImg} ";
        }

        if ($copper) {
            if ($negative) {
                $copper *= -1;
            }

            $result .= "{$copper} {$copperImg} ";
        }

        return ($result ? trim($result) : "0 {$copperImg}") . "&nbsp;";
    }

    public function getName() {
        return 'gw2money';
    }
}
