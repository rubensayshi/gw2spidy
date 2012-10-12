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
 * ----------------------
 *  route /crafting
 * ----------------------
 */
$app->get("/crafting/{discipline}/{page}", function(Request $request, $discipline, $page) use($app) {
    $app->setCraftingActive();

    $page = $page > 0 ? $page : 1;
    $itemsperpage = 50;

    $q = RecipeQuery::create();

    if ($discipline == -1) {
        $discipline = null;
    }

    if (!is_null($discipline)) {
        $discipline = DisciplineQuery::create()->findPk($discipline);
        $q->filterByDiscipline($discipline);
    }

    $sortByOptions = array('name', 'rating', 'cost', 'sell_price', 'profit');

    foreach ($sortByOptions as $sortByOption) {
        if ($request->get("sort_{$sortByOption}", null)) {
            $sortOrder = $request->get("sort_{$sortByOption}", 'asc');
            $sortBy    = $sortByOption;
        }
    }

    $sortBy    = isset($sortBy)    && in_array($sortBy, $sortByOptions)          ? $sortBy    : 'rating';
    $sortOrder = isset($sortOrder) && in_array($sortOrder, array('asc', 'desc')) ? $sortOrder : 'desc';

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

    $q->addSelectColumn("*");

    $q->offset($itemsperpage * ($page-1))
      ->limit($itemsperpage);

    if ($sortOrder == 'asc') {
        $q->addAscendingOrderByColumn($sortBy);
    } else if ($sortOrder == 'desc') {
        $q->addDescendingOrderByColumn($sortBy);
    }

    $recipes = $q->find();

    return $app['twig']->render('recipe_list.html.twig', array(
        'discipline' => $discipline,

        'page'     => $page,
        'lastpage' => $lastpage,
        'recipes'  => $recipes,

        'current_sort'       => $sortBy,
        'current_sort_order' => $sortOrder,
    ));
})
->assert('discipline', '-?\d*')
->assert('page',       '-?\d*')
->value('page', 1)
->bind('crafting');

/**
 * ----------------------
 *  route /recipe
 * ----------------------
 */
$app->get("/recipe/{dataId}", function(Request $request, $dataId) use($app) {
    $app->setCraftingActive();
    $recipe = RecipeQuery::create()->findPK($dataId);

    if (!$recipe) {
        return $app->abort(404, "Page does not exist.");
    }

    $item = $recipe->getResultItem();
    if(!$item) {
        return $app->abort(404, "Recipe not supported yet, we don't have the resulting item in the database yet [[ {$recipe->getName()} ]] [[ {$recipe->getResultItemId()} ]] ");
    }


    $tree = buildRecipeTree($item, $recipe, $app);

    return $app['twig']->render('recipe.html.twig', array(
        'recipe' => $recipe,
        'tree' => json_encode($tree),
    ));
})
->assert('dataId', '\d+')
->bind('recipe');

