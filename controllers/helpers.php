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

    if (($rarityFilter = $request->get('rarity_filter', null)) !== null && is_numeric($rarityFilter) && in_array($rarityFilter, array(0,1,2,3,4,5,6))) {
        $q->filterByRarity($rarityFilter);
    }
    if ($minLevelFilter = $request->get('min_level', null)) {
        $q->filterByRestrictionLevel($minLevelFilter, \Criteria::GREATER_EQUAL);
    }
    if ($maxLevelFilter = $request->get('max_level', null)) {
        $q->filterByRestrictionLevel($maxLevelFilter, \Criteria::LESS_EQUAL);
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

            'min_level' => $minLevelFilter,
            'max_level' => $maxLevelFilter,

            'current_sort'       => $sortBy,
            'current_sort_order' => $sortOrder,
    ));
};

/**
 * generic function used for /search and /crafting
 *
 * @param  Application   $app
 * @param  Request       $request
 * @param  ItemQuery     $q
 * @param  int           $page
 * @param  int           $itemsperpage
 * @param  array         $tplVars
 */
function recipe_list(Application $app, Request $request, RecipeQuery $q, $page, $itemsperpage, array $tplVars = array()) {
    $sortByOptions = array('name', 'rating', 'cost', 'sell_price', 'profit', 'sale_availability', 'offer_availability');

    foreach ($sortByOptions as $sortByOption) {
        if ($request->get("sort_{$sortByOption}", null)) {
            $sortOrder = $request->get("sort_{$sortByOption}", 'asc');
            $sortBy    = $sortByOption;
        }
    }

    $sortBy    = isset($sortBy)    && in_array($sortBy, $sortByOptions)          ? $sortBy    : 'profit';
    $sortOrder = isset($sortOrder) && in_array($sortOrder, array('asc', 'desc')) ? $sortOrder : 'desc';

    $minLevelFilter = $request->get('min_level', null);
    $maxLevelFilter = $request->get('max_level', null);
    if ($minLevelFilter || $maxLevelFilter) {
        $iq = $q->useResultItemQuery();
        if ($minLevelFilter)
            $iq->filterByRestrictionLevel($minLevelFilter, \Criteria::GREATER_EQUAL);
        if($maxLevelFilter)
            $iq->filterByRestrictionLevel($maxLevelFilter, \Criteria::LESS_EQUAL);
        $iq->endUse();
    }

    if ($minRatingFilter = $request->get('min_rating', null)) {
        $q->filterByRating($minRatingFilter, \Criteria::GREATER_EQUAL);
    }
    if ($maxRatingFilter = $request->get('max_rating', null)) {
        $q->filterByRating($maxRatingFilter, \Criteria::LESS_EQUAL);
    }

    if($hideLocked = $request->get('hide_unlock_required', null)) {
    	$q->filterByRequiresUnlock(0, \Criteria::EQUAL);
    }
    
    $q->innerJoinResultItem('ri')
      ->withColumn('ri.SaleAvailability','sale_availability')
      ->withColumn('ri.OfferAvailability','offer_availability')
      ->withColumn('ri.Rarity','rarity');

    
    if ($minSupplyFilter = $request->get('min_supply', null)) {
        $q->where('ri.SaleAvailability >= ?', $minSupplyFilter);
    }  
    if ($maxSupplyFilter = $request->get('max_supply', null)) {
        $q->where('ri.SaleAvailability <= ?', $maxSupplyFilter);
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


    $q->offset($itemsperpage * ($page-1))
      ->limit($itemsperpage);

    if ($sortOrder == 'asc') {
        $q->addAscendingOrderByColumn($sortBy);
    } else if ($sortOrder == 'desc') {
        $q->addDescendingOrderByColumn($sortBy);
    }

    $recipes = $q->find();

    return $app['twig']->render('recipe_list.html.twig', $tplVars + array(
        'page'     => $page,
        'lastpage' => $lastpage,
        'recipes'  => $recipes,

        'min_level' => $minLevelFilter,
        'max_level' => $maxLevelFilter,
        'min_rating' => $minRatingFilter,
        'max_rating' => $maxRatingFilter,
        'hide_unlock_required' => $hideLocked,
        
        'min_supply' => $minSupplyFilter,
        'max_supply' => $maxSupplyFilter,

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

// see http://en.wikipedia.org/wiki/Greatest_common_divisor#Using_Euclid.27s_algorithm
function gcd($a, $b) {
   if($a <= 0 || $b <=0)
       return 1;
   if ($a == $b)
       return $a;
   return $a > $b ? gcd($a - $b, $b) : gcd($a, $b - $a);
}

// returns the amount of times we have to craft the base recipe in order to get a multiple of the required amount
function multiplier($base, $multiple) {
   return $base / gcd($base, $multiple);
}

function calculateRecipeMultiplier($item, $recipe = null) {

    if($recipe) {
        $multiplier = 1;

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientItem   = $ingredient->getItem();
            $ingredientRecipe = null;

            $ingredientRecipes = $ingredientItem->getResultOfRecipes();

            if (count($ingredientRecipes)) {
                $ingredientRecipe = $ingredientRecipes[0];

                $baseCount = $ingredientRecipe->getCount() * calculateRecipeMultiplier($ingredientItem, $ingredientRecipe);
                $multiplier *= multiplier($baseCount, $multiplier * $ingredient->getCount());
            }
        }

        return $multiplier;
    }

    return 1;
}

function buildMultiRecipeTree($item, $recipe = null, $app) {
    return buildRecipeTree($item, $recipe, $app, calculateRecipeMultiplier($item, $recipe));
}

function buildRecipeTree($item, $recipe = null, $app, $multiplier = 1) {
    $tree = array(
        'id' => $item->getDataId(),
        'name' => $item->getName(),
        'href' => $app['url_generator']->generate('item', array('dataId' => $item->getDataId())),
        'gw2db_href' => "http://www.gw2db.com/items/{$item->getGw2dbExternalId()}-" . Functions::slugify($item->getName()),
        'rarity' => $item->getRarityName(),
        'img'	=> $item->getImg(),
        'price' => $item->getBestPrice(),
        'vendor' => !!$item->getVendorPrice(),
        'multiplier' => $multiplier,
        'karma' => $item->getKarmaPrice(),
    );

    if ($recipe) {
        $recipeTree = array();

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientItem   = $ingredient->getItem();
            $ingredientRecipe = null;

            $ingredientRecipes = $ingredientItem->getResultOfRecipes();

            $ingredientMultiplier = $multiplier;
            if (count($ingredientRecipes)) {
                $ingredientRecipe = $ingredientRecipes[0];
                $ingredientMultiplier /= multiplier($ingredientRecipe->getCount(), $ingredient->getCount());
            }

            $recipeTree[] = array(buildRecipeTree($ingredientItem, $ingredientRecipe, $app, $ingredientMultiplier), $ingredient->getCount() * $multiplier);
        }

        $tree['recipe'] = array('count' => $recipe->getCount() * $multiplier, 'ingredients' => $recipeTree);
    }

    return $tree;
}

