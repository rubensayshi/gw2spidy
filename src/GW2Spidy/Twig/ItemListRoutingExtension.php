<?php

namespace GW2Spidy\Twig;

use GW2Spidy\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class ItemListRoutingExtension extends \Twig_Extension {
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator) {
        $this->generator = $generator;
    }

    public function getFunctions() {
        return array(
            'item_list_path' => new \Twig_Function_Method($this, 'getPath'),
        );
    }

    public function getPath($context, $parameters = array()) {
        if ($context['search']) {
            $name = 'search';
            $parameters['search']  = $context['search'];
        } else if ($context['type']) {
            $name = 'type';
            $parameters['type']    = $context['type'];
            $parameters['subtype'] = $context['subtype'];
        }

        $sortBy    = null;
        $sortOrder = null;
        foreach ($parameters as $k => $v) {
            if (preg_match('/^sort_(.*)/', $k, $a)) {
                $sortBy    = $a[1];
                $sortOrder = $v;
            }
        }

        if ((!$sortBy || !$sortOrder) && isset($context['current_sort'], $context['current_sort_order'])) {
            $parameters["sort_{$context['current_sort']}"] = $context['current_sort_order'];
        }

        return $this->generator->generate($name, $parameters, false);
    }

    public function getName() {
        return 'item_list_routing';
    }
}
