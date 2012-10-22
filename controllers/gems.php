<?php


use \DateTime;

use GW2Spidy\Application;
use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\Dataset\DatasetManager;
use GW2Spidy\Dataset\GemExchangeDataset;

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
    	'name'     => "Gold To Gems Daily Average",
    	'visible'  => true,
        'gw2money' => true,
    );
    if (false) {
        $chart[] = array(
            'data'     => $goldToGem->getWeeklyMvAvgDataForChart(),
        	'name'     => "Gold To Gems Weekly Average",
        	'visible'  => false,
            'gw2money' => true,
        );

        /*---------------------
         *  SELL GEMS FOR GOLD
        *----------------------*/
        $goldToGem = new GemExchangeDataset(GemExchangeDataset::TYPE_GOLD_TO_GEM);
        $chart[] = array(
            'data'     => $goldToGem->getRawDataForChart(),
            'name'     => "Gems to Gold Raw Data",
        	'visible'  => true,
            'gw2money' => true,
        );
        $chart[] = array(
            'data'     => $goldToGem->getDailyMvAvgDataForChart(),
        	'name'     => "Gems to Gold Daily Average",
        	'visible'  => true,
            'gw2money' => true,
        );
        $chart[] = array(
            'data'     => $goldToGem->getWeeklyMvAvgDataForChart(),
        	'name'     => "Gems to Gold Weekly Average",
        	'visible'  => false,
            'gw2money' => true,
        );
    }

    $content = json_encode($chart);

    return $content;
})
->bind('gem_chart');

