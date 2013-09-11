<?php

use GW2Spidy\DB\Watchlist;
use GW2Spidy\DB\WatchlistQuery;
use GW2Spidy\DB\ItemQuery;
use Symfony\Component\HttpFoundation\Request;
/**
 * ----------------------
 *  route /watchlistadd POST
 * ----------------------
 */
$app->get("/watchlist/add/{dataId}", function (Request $request, $dataId) use ($app) {
    if (!($user = $app['user'])) {
        return $app->redirect($app['url_generator']->generate('login'));
    }
    
    if ($item = ItemQuery::create()->findPk($dataId)) {
        // check unique
        if (!WatchlistQuery::create()->filterByUser($user)->filterByItem($item)->count()) {
            $w = new Watchlist();
            $w->setUser($user);
            $w->setItemId($dataId);

            $w->save();
        }
    }

    $uri = $request->headers->get('referer');
    if ($uri && preg_match('/\/login/', $uri)) {
        $uri = null;
    }

    return $app->redirect($uri ?: $app['url_generator']->generate('watchlist'));
})
->assert('dataId', '\d+')
->bind('watchlistaddpost');

/**
 * ----------------------
 *  route /watchlistremove POST
 * ----------------------
 */
$app->get("/watchlist/remove/{dataId}", function (Request $request, $dataId) use ($app) {
    if (!($user = $app['user'])) {
        return $app->redirect($app['url_generator']->generate('login'));
    }

    if ($item = ItemQuery::create()->findPk($dataId)) {
        $w = WatchlistQuery::create();
        $w->filterByItem($item);
        $w->filterByUser($user);
        $w->delete();
    }
    return $app->redirect($app['url_generator']->generate('watchlist'));
})
->assert('dataId', '\d+')
->bind('watchlistremovepost');

/**
 * ----------------------
 *  route /watchlistremoveall POST
 * ----------------------
 */
$app->get("/watchlist/removeall", function (Request $request) use ($app) {
    if (!($user = $app['user'])) {
        return $app->redirect($app['url_generator']->generate('login'));
    }
    
    $w = WatchlistQuery::create();
    $w->filterByUser($user);
    $w->delete();
    
    return $app->redirect($app['url_generator']->generate('watchlist'));
})
->bind('watchlistremoveall');

/**
 * ----------------------
 *  route /watchlist GET
 * ----------------------
 */
$app->get("/watchlist/{page}", function(Request $request, $page) use($app) {
    if (!($user = $app['user'])) {
        return $app->redirect($app['url_generator']->generate('login'));
    }

    $itemIds = array();
    if ($ws = $user->getWatchlists()) {
        foreach ($ws as $w) {
            $itemIds[] = $w->getItemId();
        }
    }

    $page = $page > 0 ? $page : 1;

    $q = ItemQuery::create();
    $q->filterByPrimaryKeys(array_unique($itemIds));

    // use generic function to render
    return item_list($app, $request, $q, $page, 25, array('watchlist'=> true));
})
->assert('page', '\d*')
->value('page', 1)
->bind('watchlist');

