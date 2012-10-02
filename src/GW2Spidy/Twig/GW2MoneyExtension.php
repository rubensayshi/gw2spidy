<?php

namespace GW2Spidy\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class GW2MoneyExtension extends \Twig_Extension {
    protected $env;
    protected $assetRouting;

    public function getFilters() {
        return array(
            'gw2money' => new \Twig_Filter_Method($this, 'getGW2Money' ,  array('is_safe' => array('html'))),
        );
    }

    public function initRuntime(\Twig_Environment $env) {
        $this->env = $env;
        $this->assetRouting = $this->env->getExtension('asset_routing');
    }

    public function getMoneyImagePath($path) {
        if ($this->assetRouting) {
            $path = $this->assetRouting->getAssetPath($path);
        }

        return $path;
    }

    public function getGW2Money($copper) {
        $goldImg   = '<img src="' . $this->getMoneyImagePath('/assets/img/gold.png') . '" /> ';
        $silverImg = '<img src="' . $this->getMoneyImagePath('/assets/img/silver.png') . '" /> ';
        $copperImg = '<img src="' . $this->getMoneyImagePath('/assets/img/copper.png') . '" /> ';

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
