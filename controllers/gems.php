<?php

use GW2Spidy\DB\DisciplineQuery;

use GW2Spidy\DB\ItemSubTypeQuery;

use GW2Spidy\DB\ItemType;

use GW2Spidy\DB\RecipeQuery;

use GW2Spidy\Twig\GenericHelpersExtension;

use GW2Spidy\GW2SessionManager;

use \DateTime;

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

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\Application;

use GW2Spidy\Twig\VersionedAssetsRoutingExtension;
use GW2Spidy\Twig\ItemListRoutingExtension;
use GW2Spidy\Twig\GW2MoneyExtension;

use GW2Spidy\NewQueue\RequestSlotManager;
use GW2Spidy\NewQueue\QueueHelper;

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
    $goldToGem = GoldToGemRateQuery::getChartDatasetData();
    $chart[] = array(
        'data'     => $goldToGem['raw'],
        'name'     => "Gold To Gems Raw Data",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $goldToGem['daily'],
    	'name'     => "Gold To Gems Daily Average",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $goldToGem['weekly'],
    	'name'     => "Gold To Gems Weekly Average",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $goldToGem['monthly'],
    	'name'     => "Gold To Gems 30-day Average",
    	'visible'  => false,
        'gw2money' => true,
    );

    /*---------------------
     *  SELL GEMS FOR GOLD
    *----------------------*/
    $gemToGold = GemToGoldRateQuery::getChartDatasetData();
    $chart[] = array(
        'data'     => $gemToGold['raw'],
        'name'     => "Gems to Gold Raw Data",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $gemToGold['daily'],
    	'name'     => "Gems to Gold Daily Average",
    	'visible'  => true,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $gemToGold['weekly'],
    	'name'     => "Gems to Gold Weekly Average",
    	'visible'  => false,
        'gw2money' => true,
    );
    $chart[] = array(
    	'data'     => $gemToGold['monthly'],
    	'name'     => "Gems to Gold 30-day Average",
    	'visible'  => false,
        'gw2money' => true,
    );

    $content = json_encode($chart);

    return $content;
})
->bind('gem_chart');

