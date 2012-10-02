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
        
        $goldImg   = '<img src="/assets/img/gold.png" /> ';
        $silverImg = '<img src="/assets/img/silver.png" /> ';
        $copperImg = '<img src="/assets/img/copper.png" /> ';
        
        $copper = intval($copper);
        $result = "";

        if ($gold = floor($copper / 10000)) {
            $copper = $copper % ($gold * 10000);
            $result .= "{$gold} {$goldImg} ";
        }

        if ($silver = floor($copper / 100)) {
            $copper = $copper % ($silver * 100);
            $result .= "{$silver} {$silverImg} ";
        }

        if ($copper) {
            $result .= "{$copper} {$copperImg} ";
        }

        return $result ? trim($result) : "0 {$copperImg}";
    }

    public function getName() {
        return 'gw2money';
    }
}
