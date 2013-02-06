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
            'recipe_list_path' => new \Twig_Function_Method($this, 'getRecipePath'),
        );
    }

    public function getPath($context, $parameters = array()) {
        if (array_key_exists('search', $context)) {
            $name = 'search';
            $parameters['search'] = $context['search'];
        }
        else if (array_key_exists('watchlist', $context) && $context['watchlist']) {
            $name = 'watchlist';
            $parameters['watchlist'] = $context['watchlist'];
        }
        else if (array_key_exists('type', $context)) {
            $name = 'type';
            $parameters['type']    = $context['type'] ? $context['type']->getId() : -1;
            $parameters['subtype'] = $context['subtype'] ? $context['subtype']->getId() : -1;
        } else {
            throw new \Exception("invalid context " . var_export(array_keys($context), true));
        }

        if (isset($context['rarity_filter']) && !array_key_exists('rarity_filter', $parameters)) {
            $parameters['rarity_filter'] = $context['rarity_filter'];
        }
        if (isset($context['min_level']) && !array_key_exists('min_level', $parameters)) {
            $parameters['min_level'] = $context['min_level'];
        }
        if (isset($context['max_level']) && !array_key_exists('max_level', $parameters)) {
            $parameters['max_level'] = $context['max_level'];
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

    public function getRecipePath($context, $parameters = array()) {
        if (array_key_exists('search', $context)) {
            $name = 'search';
            $parameters['search'] = $context['search'];
            $parameters['recipes'] = 1;
        }
        else if (array_key_exists('discipline', $context)) {
            $name = 'crafting';
            $parameters['discipline'] = $context['discipline'] ? $context['discipline']->getId() : -1;
        } else {
            throw new \Exception("invalid context " . var_export(array_keys($context), true));
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
        if (isset($context['min_level']) && !array_key_exists('min_level', $parameters)) {
            $parameters['min_level'] = $context['min_level'];
        }
        if (isset($context['max_level']) && !array_key_exists('max_level', $parameters)) {
            $parameters['max_level'] = $context['max_level'];
        }
        if (isset($context['hide_unlock_required']) && !array_key_exists('hide_unlock_required', $parameters)) {
            $parameters['hide_unlock_required'] = $context['hide_unlock_required'];
        }

        return $this->generator->generate($name, $parameters, false);
    }

    public function getName() {
        return 'item_list_routing';
    }
}
