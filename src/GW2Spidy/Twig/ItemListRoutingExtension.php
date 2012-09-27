<?php

namespace GW2Spidy\Twig;

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
        if (array_key_exists('search', $context)) {
            $name = 'search';
            $parameters['search'] = $context['search'];
        } else if (array_key_exists('type', $context)) {
            $name = 'type';
            $parameters['type']    = $context['type'];
            $parameters['subtype'] = $context['subtype'];
        } else {
            throw new \Exception("invalid context " . var_export(array_keys($context), true));
        }

        if (isset($context['rarity_filter']) && !array_key_exists('rarity_filter', $parameters)) {
            $parameters['rarity_filter'] = $context['rarity_filter'];
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
