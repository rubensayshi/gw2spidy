<?php

use GW2Spidy\Dataset\ItemVolumeDataset;

use GW2Spidy\Dataset\ItemDataset;

use GW2Spidy\Dataset\DatasetManager;

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
 *  route /types
 * ----------------------
 */
$app->get("/types", function() use($app) {
    $types = ItemTypeQuery::getAllTypes();

    return $app['twig']->render('types.html.twig', array(
        'types' => $types,
    ));
})
->bind('types');

/**
 * ----------------------
 *  route /type
 * ----------------------
 */
$app->get("/type/{type}/{subtype}/{page}", function(Request $request, $type, $subtype, $page) use($app) {
    $page = $page > 0 ? $page : 1;

    $q = ItemQuery::create();

    if ($type == -1) {
        $type = null;
    }
    if ($subtype == -1) {
        $subtype = null;
    }

    if (!is_null($type)) {
        if (!($type = ItemTypeQuery::create()->findPk($type))) {
            return $app->abort(404, "bad type");
        }
        $q->filterByItemType($type);

        if (!is_null($subtype)) {
            if (!($subtype = ItemSubTypeQuery::create()->findPk(array($subtype, $type->getId())))) {
                return $app->abort(404, "bad type");
            }
            $q->filterByItemSubType($subtype);
        }
    }

    // use generic function to render
    return item_list($app, $request, $q, $page, 50, array('type' => $type, 'subtype' => $subtype));
})
->assert('type',     '-?\d*')
->assert('subtype',  '-?\d*')
->assert('page',     '-?\d*')
->value('type',      -1)
->value('subtype',   -1)
->value('page',      1)
->bind('type');

/**
 * ----------------------
 *  route /item
 * ----------------------
 */
$app->get("/item/{dataId}", function($dataId) use ($app) {
    $item = ItemQuery::create()
            ->leftJoinBuyListing('b')
            ->withColumn('MAX(b.listing_datetime)', 'BuyUpdated')
            ->leftJoinSellListing('s')
            ->withColumn('MAX(s.listing_datetime)', 'SellUpdated')
            ->findOneByDataId($dataId);
    
    $ingredientInRecipes = RecipeQuery::create()
        ->useIngredientQuery()
            ->filterByItemId($dataId)
        ->endUse()
        ->addDescendingOrderByColumn('profit')
        ->find();

    if (!$item) {
        return $app->abort(404, "Page does not exist.");
    }

    return $app['twig']->render('item.html.twig', array(
        'item'                => $item,
        'ingredientInRecipes' => $ingredientInRecipes,
    ));
})
->assert('dataId',  '\d+')
->bind('item');

/**
 * ----------------------
 *  route /chart
 * ----------------------
 */
$app->get("/chart/{dataId}", function($dataId) use ($app) {
    $item = ItemQuery::create()->findPK($dataId);

    if (!$item) {
        return $app->abort(404, "Page does not exist.");
    }

    $chart = array();

    $sellListings = DatasetManager::getInstance()->getItemDataset($item, ItemDataset::TYPE_SELL_LISTING);
    $sellListingsVolume = DatasetManager::getInstance()->getItemVolumeDataset($item, ItemVolumeDataset::TYPE_SELL_LISTING);
    $buyListings = DatasetManager::getInstance()->getItemDataset($item, ItemDataset::TYPE_BUY_LISTING);
    $buyListingsVolume = DatasetManager::getInstance()->getItemVolumeDataset($item, ItemVolumeDataset::TYPE_BUY_LISTING);

    /*---------------
     *  MV AVG VOLUME SELL
    *---------------*/
    $chart[] = array(
        'data'     => $sellListingsVolume->getDailyMvAvgDataForChart(),
        'name'    => "Sell Listings Volume 1 Day Mv Avg",
        'visible' => true,
        'yAxis'   => 1,
        'type'    => 'column',
    );
    /*---------------
     *  MV AVG VOLUME BUY
    *---------------*/
    $chart[] = array(
        'data'     => $buyListingsVolume->getDailyMvAvgDataForChart(),
        'name'    => "Buy Listings Volume 1 Day Mv Avg",
        'visible' => true,
        'yAxis'   => 1,
        'type'    => 'column',
    );

    /*----------------
     *  SELL LISTINGS
    *----------------*/
    $chart[] = array(
        'data'     => $sellListings->getNoMvAvgDataForChart(),
        'name'     => "Sell Listings Raw Data",
        'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $sellListings->getDailyMvAvgDataForChart(),
        'name'     => "Sell Listings 1 Day Mv Avg",
        'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $sellListingsVolume->getNoMvAvgDataForChart(),
    	'name'    => "Sell Listings Volume",
    	'visible' => false,
        'yAxis'   => 1,
        'type'    => 'column',
    );

    /*----------------
     *  BUY LISTINGS
     *----------------*/
    $chart[] = array(
        'data'     => $buyListings->getNoMvAvgDataForChart(),
        'name'     => "Buy Listings Raw Data",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $buyListings->getDailyMvAvgDataForChart(),
    	'name'     => "Buy Listings 1 Day Mv Avg",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $buyListingsVolume->getNoMvAvgDataForChart(),
    	'name'    => "Buy Listings Volume",
    	'visible' => false,
        'yAxis'   => 1,
        'type'    => 'column',
    );

    $content = json_encode($chart);

    return $content;
})
->assert('dataId',  '\d+')
->bind('chart');

