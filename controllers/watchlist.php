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

use GW2Spidy\Util\Functions;

/**
 * ----------------------
 *  route /watchlist POST
 * ----------------------
 */
$app->post("/watchlist", function (Request $request) use ($app) {    
    $id = $request->get("data_id");
    
    $c = $request->cookies->get("watchlist");
    $c->value[] = $id;
    
    // redirect to the GET with the search in the URL
    $response = $app->redirect($app['url_generator']->generate('watchlist'));    
    if($c){
        $response->headers->setCookie(new Cookie('watchlist',$c->value));
    }else{
        $response->headers->setCookie(new Cookie('watchlist',array($id)));
    }
    return $response;
})
->bind('watchlistpost');

/**
 * ----------------------
 *  route /watchlist GET
 * ----------------------
 */
$app->get("/watchlist/{page}", function(Request $request, $page) use($app) {
    
    $watchlistCookie = $request->cookies->get("watchlist");
    
    $page = $page > 0 ? $page : 1;

    $q = ItemQuery::create();
    //foreach($watchlistCookie as $w){
        $q->filterByDataId($watchlistCookie[0]);
    //}

//    if ($page == 1 && $q->count() == 1) {
//        $item = $q->findOne();
//        return $app->redirect($app['url_generator']->generate('item', array('dataId' => $item->getDataId())));
//    }

    // use generic function to render
    return item_list($app, $request, $q, $page, 25, array('search' => $search));
})
->value('page',      1)
->bind('watchlist');

