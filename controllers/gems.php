<?php

use GW2Spidy\Dataset\DatasetManager;
use GW2Spidy\Dataset\GemExchangeDataset;

/**
 * ----------------------
 *  route /gem
 * ----------------------
 */
$app->get("/gem", function() use($app) {
    // workaround for now to set active menu item
    $app->setGemActive();

    $summary = gem_summary();

    return $app['twig']->render('gem.html.twig', (array)$summary);
})
->bind('gem');

/**
 * ----------------------
 *  route /gem_chart
 * ----------------------
 */
$app->get("/gem_chart", function() use($app) {
    $chart = array();

    /*---------------------
     *  BUY GEMS WITH GOLD
     *----------------------*/
    $goldToGem = DatasetManager::getInstance()->getGemDataset(GemExchangeDataset::TYPE_GOLD_TO_GEM);
    $chart[] = array(
        'data'     => $goldToGem->getNoMvAvgDataForChart(),
        'name'     => "Gold To Gems Raw Data",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $goldToGem->getDailyMvAvgDataForChart(),
    	'name'     => "Gold To Gems 1 Day Mv Avg",
    	'visible'  => true,
        'gw2money' => true,
    );

    /*---------------------
     *  SELL GEMS FOR GOLD
     *----------------------*/
    $gemToGold = DatasetManager::getInstance()->getGemDataset(GemExchangeDataset::TYPE_GEM_TO_GOLD);
    $chart[] = array(
        'data'     => $gemToGold->getNoMvAvgDataForChart(),
        'name'     => "Gems to Gold Raw Data",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
        'data'     => $gemToGold->getDailyMvAvgDataForChart(),
    	'name'     => "Gems to Gold 1 Day Mv Avg",
    	'visible'  => true,
        'gw2money' => true,
    );

    $content = json_encode($chart);

    return $content;
})
->bind('gem_chart');

