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
->assert('type',     '-?\d+')
->assert('subtype',  '-?\d+')
->assert('page',     '-?\d+')
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
    $item = ItemQuery::create()->findPK($dataId);

    if (!$item) {
        return $app->abort(404, "Page does not exist.");
    }

    return $app['twig']->render('item.html.twig', array(
        'item'        => $item,
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

    /*----------------
     *  SELL LISTINGS
     *----------------*/
    $sellListings = SellListingQuery::getChartDatasetDataForItem($item);
    $chart[] = array(
        'data'     => $sellListings['raw'],
        'name'     => "Sell Listings Raw Data",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $sellListings['daily'],
    	'name'     => "Sell Listings Daily Average",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $sellListings['weekly'],
    	'name'     => "Sell Listings Weekly Average",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $sellListings['monthly'],
    	'name'     => "Sell Listings 30-day Average",
    	'visible'  => false,
        'gw2money' => true,
    );

    /*---------------
     *  BUY LISTINGS
     *---------------*/
    $buyListings = BuyListingQuery::getChartDatasetDataForItem($item);
    $chart[] = array(
        'data'     => $buyListings['raw'],
        'name'     => "Buy Listings Raw Data",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $buyListings['daily'],
    	'name'     => "Buy Listings Daily Average",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $buyListings['weekly'],
    	'name'     => "Buy Listings Weekly Average",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $buyListings['monthly'],
    	'name'     => "Buy Listings 30-day Average",
    	'visible'  => false,
        'gw2money' => true,
    );

    /*---------------
     *  VOLUME
     *---------------*/
    $chart[] = array(
    	'data'    => $sellListings['cnt'],
    	'name'    => "Sell Listings Volume",
    	'visible' => false,
        'yAxis'   => 1,
        'type'    => 'column',
    );
    $chart[] = array(
    	'data'    => $sellListings['daily_cnt'],
    	'name'    => "Sell Listings Volume Daily Avg",
    	'visible' => true,
        'yAxis'   => 1,
        'type'    => 'column',
    );
    $chart[] = array(
    	'data'    => $buyListings['cnt'],
    	'name'    => "Buy Listings Volume",
    	'visible' => false,
        'yAxis'   => 1,
        'type'    => 'column',
    );
    $chart[] = array(
    	'data'    => $buyListings['daily_cnt'],
    	'name'    => "Buy Listings Volume Daily Avg",
    	'visible' => true,
        'yAxis'   => 1,
        'type'    => 'column',
    );

    $content = json_encode($chart);

    return $content;
})
->assert('dataId',  '\d+')
->bind('chart');

