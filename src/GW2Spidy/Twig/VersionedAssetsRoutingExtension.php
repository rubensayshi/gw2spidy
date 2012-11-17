<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class VersionedAssetsRoutingExtension extends \Twig_Extension {
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator) {
        $this->generator = $generator;
    }

    public function getFunctions() {
        return array(
            'versioned_asset' => new \Twig_Function_Method($this, 'getAssetPath'),
        );
    }

    protected function getVersionString() {
        return Application::getInstance()->getVersionString();
    }

    public function getAssetPath($path, $parameters = array()) {
        if ($version = $this->getVersionString()) {
            $path = str_replace("/assets/", "/assets/v{$this->getVersionString()}/", $path);
        }

        $context = $this->generator->getContext();

        return "{$context->getBaseUrl()}{$path}";
    }

    public function getName() {
        return 'asset_routing';
    }
}
