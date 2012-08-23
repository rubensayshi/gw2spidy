<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class VersionedAssetsRoutingExtension extends \Twig_Extension {
    public function getFunctions() {
        return array(
            'versioned_asset' => new \Twig_Function_Method($this, 'getAssetPath'),
        );
    }

    protected function getVersionString() {
        return Application::getInstance()->getVersionString();
    }

    public function getAssetPath($name, $parameters = array()) {
        if ($version = $this->getVersionString()) {
            return str_replace("/assets/", "/assets/v{$this->getVersionString()}/", $name);
        } else {
            return $name;
        }
    }

    public function getName() {
        return 'asset_routing';
    }
}
