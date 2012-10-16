<?php

use \DateTime;

use GW2Spidy\Application;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

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
use GW2Spidy\DB\Watchlist;
use GW2Spidy\DB\WatchlistQuery;

use GW2Spidy\Util\Functions;

/**
 * ----------------------
 *  route /watchlistadd POST
 * ----------------------
 */
$app->post("/watchlistadd", function (Request $request) use ($app) {    
    $data_id = $request->get("data_id");
    if($data_id){
        
        // TODO: Test if item is valid item!
        
        $w = new Watchlist();
        $w->setUserId(1); //TODO: replace with real user
        $w->setItemId($data_id);
        $w->save();
    }
    //TODO: check what happens when referer is disabled in browser
    $uri = $request->headers->get('referer');
    return $app->redirect($uri);
})
->bind('watchlistaddpost');

/**
 * ----------------------
 *  route /watchlistremove POST
 * ----------------------
 */
$app->post("/watchlistremove", function (Request $request) use ($app) {    
    $data_id = $request->get("data_id");
    if($data_id){
        
        // TODO: Test if item is valid item!
        
        $w = WatchlistQuery::create();
        $w->filterByItemId($data_id);
        $w->filterByUserId(1);  //TODO: replace with real user
        $w->delete();
    }
    return $app->redirect($app['url_generator']->generate('watchlist'));
})
->bind('watchlistremovepost');

/**
 * ----------------------
 *  route /watchlist GET
 * ----------------------
 */
$app->get("/watchlist/{page}", function(Request $request, $page) use($app) {
    $itemIds = array();
    $w = WatchlistQuery::create()->findByUserId(1);
    if($w){
        $itemIds = array();
        foreach($w as $item){
            $itemIds[] = $item->getItemId();
        }
    }
    $page = $page > 0 ? $page : 1;
    $q = ItemQuery::create();
    $q->filterByPrimaryKeys($itemIds);
    // use generic function to render
    return item_list($app, $request, $q, $page, 25,array('watchlist'=> true));
})
->value('page',      1)
->bind('watchlist');

