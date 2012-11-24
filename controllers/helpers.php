<?php

use \DateTime;

use GW2Spidy\Application;
use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\DB\DisciplineQuery;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\RecipeQuery;
use GW2Spidy\DB\GW2Session;
use GW2Spidy\DB\GoldToGemRateQuery;
use GW2Spidy\DB\GemToGoldRateQuery;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\ItemPeer;
use GW2Spidy\DB\BuyListingPeer;
use GW2Spidy\DB\SellListingPeer;
use GW2Spidy\DB\BuyListingQuery;

use GW2Spidy\Util\Functions;

/**
 * lambda used to convert URL arguments
 *
 * @param  mixed     $val
 * @return int
 */
$toInt = function($val) {
    return (int) $val;
};

/**
 * generic function used for /search and /type
 *
 * @param  Application   $app
 * @param  Request       $request
 * @param  ItemQuery     $q
 * @param  int           $page
 * @param  int           $itemsperpage
 * @param  array         $tplVars
 */
function item_list(Application $app, Request $request, ItemQuery $q, $page, $itemsperpage, array $tplVars = array()) {
    $sortByOptions = array('name', 'rarity', 'restriction_level', 'min_sale_unit_price', 'max_offer_unit_price', 'sale_availability', 'offer_availability', 'margin');

    foreach ($sortByOptions as $sortByOption) {
        if ($request->get("sort_{$sortByOption}", null)) {
            $sortOrder = $request->get("sort_{$sortByOption}", 'asc');
            $sortBy    = $sortByOption;
        }
    }

    $sortBy    = isset($sortBy)    && in_array($sortBy, $sortByOptions)          ? $sortBy    : 'name';
    $sortOrder = isset($sortOrder) && in_array($sortOrder, array('asc', 'desc')) ? $sortOrder : 'asc';

    if (($rarityFilter = $request->get('rarity_filter', null)) !== null && in_array($rarityFilter, array(0,1,2,3,4,5,6))) {
        $q->filterByRarity($rarityFilter);
    }

    $count = $q->count();

    if ($count > 0) {
        $lastpage = ceil($count / $itemsperpage);
        if ($page > $lastpage) {
            $page = $lastpage;
        }
    } else {
        $page     = 1;
        $lastpage = 1;
    }

    $q->addAsColumn("margin", "min_sale_unit_price * 0.85 - max_offer_unit_price");
    $q->addSelectColumn("*");

    $q->offset($itemsperpage * ($page-1))
      ->limit($itemsperpage);

    if ($sortOrder == 'asc') {
        $q->addAscendingOrderByColumn($sortBy);
    } else if ($sortOrder == 'desc') {
        $q->addDescendingOrderByColumn($sortBy);
    }

    $items = $q->find();

    return $app['twig']->render('item_list.html.twig', $tplVars + array(
            'page'     => $page,
            'lastpage' => $lastpage,
            'items'    => $items,

            'rarity_filter' => $rarityFilter,

            'current_sort'       => $sortBy,
            'current_sort_order' => $sortOrder,
    ));
};

function gem_summary() {
    $lastSell = GemToGoldRateQuery::create()
                ->addDescendingOrderByColumn("rate_datetime")
                ->offset(-1)
                ->limit(1)
                ->findOne();

    $lastBuy = GoldToGemRateQuery::create()
                ->addDescendingOrderByColumn("rate_datetime")
                ->offset(-1)
                ->limit(1)
                ->findOne();

    if (!$lastSell || !$lastBuy) {
        return null;
    }

    $gemtogold = $lastSell->getRate();
    $goldtogem = $lastBuy->getRate();

    $usdtogem    = 10  / 800 * 100;
    $poundstogem = 8.5 / 800 * 100;
    $eurostogem  = 10  / 800 * 100;

    $usdtogold   = (10000 / $gemtogold) * $usdtogem;

    return array(
        'gemtogold' => $gemtogold,
        'goldtogem' => $goldtogem,
        'usdtogem'  => $usdtogem,
        'usdtogold' => $usdtogold,
    );
}

function buildRecipeTree($item, $recipe = null, $app) {
    $tree = array(
        'id' => $item->getDataId(),
        'name' => $item->getName(),
        'href' => $app['url_generator']->generate('item', array('dataId' => $item->getDataId())),
        'gw2db_href' => "http://www.gw2db.com/items/{$item->getGw2dbExternalId()}-" . Functions::slugify($item->getName()),
        'rarity' => $item->getRarityName(),
        'img'	=> $item->getImg(),
        'price' => $item->getBestPrice(),
        'vendor' => !!$item->getVendorPrice()
    );

    if ($recipe) {
        $recipeTree = array();

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientItem   = $ingredient->getItem();
            $ingredientRecipe = null;

            $ingredientRecipes = $ingredientItem->getResultOfRecipes();

            if (count($ingredientRecipes)) {
                $ingredientRecipe = $ingredientRecipes[0];
            }

            $recipeTree[] = array(buildRecipeTree($ingredientItem, $ingredientRecipe, $app), $ingredient->getCount());
        }

        $tree['recipe'] = array('count' => $recipe->getCount(), 'ingredients' => $recipeTree);
    }

    return $tree;
}

