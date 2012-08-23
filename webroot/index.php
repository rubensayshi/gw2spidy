<?php

use GW2Spidy\DB\BuyListingQuery;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\HttpFoundation\Request;

use GW2Spidy\Application;

use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\DB\WorkerQueueItemQuery;

use GW2Spidy\Queue\RequestSlotManager;
use GW2Spidy\Queue\WorkerQueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$app = Application::getInstance();
$app->isSQLLogMode() && $app->enableSQLLogging();
$app->isDevMode()    && $app['debug'] = true;

/*
 * temporary check so other people who are rebasing don't get screwed over because I changed the config ;)
 */
if (!defined('TRADINGPOST_URL')) {
    echo <<<EOD
<h1>DEBUG</h1>
<hr />
Hey friendly person forking my project ;) <br />
<br />
I changed the config.inc.php file a bit, you should copy the config.inc.example.php and fill in your preferences again. <br />
Sorry for that, but I felt it would be better to put only the 2 base URLs there so if needed it's easy to switch them around. <br />
<br />
I also added the DEV_MODE and SQL_LOG_MODE, you should enable DEV_MODE at least unless you're running the code in production. <br />
<br />
Greetz, <br />
<br />
Ruben // Drakie
EOD;

die();
}

$toInt = function($val) {
    return (int) $val;
};

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'    => dirname(__FILE__) . '/../templates',
    'twig.options' => array(
        'cache' => dirname(__FILE__) . '/../tmp/twig-cache',
    ),
));

/**
 * ----------------------
 *  route /
 * ----------------------
 */
$app->get("/", function() use($app) {
    $app->setHomeActive();
    $featured = ItemQuery::create()->findPk(1140);

    return $app['twig']->render('index.html.twig', array(
        'featured' => $featured,
    ));
});

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
});

/**
 * ----------------------
 *  route /type
 * ----------------------
 */
$app->get("/type/{type}/{subtype}/{page}", function($type, $subtype, $page) use($app) {
    $itemsperpage = 50;
    $baseurl      = "/type/{$type}/{$subtype}";
    $q            = ItemQuery::create();

    if (!is_null($type)) {
        $q->filterByItemTypeId($type);
    }
    if (!is_null($subtype)) {
        $q->filterByItemSubTypeId($subtype);
    }

    $count    = $q->count();
    $lastpage = ceil($count / $itemsperpage);
    if ($page > $lastpage) {
        $page = $lastpage;
    }

    $items = $q->offset($itemsperpage * ($page-1))
                    ->orderBy("Name", "ASC")
                    ->limit($itemsperpage)
                    ->find();

    return $app['twig']->render('type.html.twig', array(
        'page'     => $page,
        'lastpage' => $lastpage,
        'items'    => $items,
        'baseurl'  => $baseurl,
    ));
})
->assert('type',     '\d+')
->assert('subtype',  '\d+')
->assert('page',     '\d+')
->convert('type',    $toInt)
->convert('subtype', $toInt)
->convert('page',    $toInt)
->value('type',      null)
->value('subtype',   null)
->value('page',      1);

/**
 * ----------------------
 *  route /item
 * ----------------------
 */
$app->get("/item/{dataId}", function($dataId) use ($app) {
    $item = ItemQuery::create()->findPK($dataId);

    return $app['twig']->render('item.html.twig', array(
        'item'        => $item,
    ));
})
->assert('dataId',  '\d+')
->convert('dataId', $toInt);

/**
 * ----------------------
 *  route /chart
 * ----------------------
 */
$app->get("/chart/{dataId}", function($dataId) use ($app) {
    $item = ItemQuery::create()->findPK($dataId);

    $chart = array();

    /*----------------
     *  SELL LISTINGS
     *----------------*/
    $chart[] = array(
        'data'   => SellListingQuery::getChartDatasetDataForItem($item),
        'label'  => "Sell Listings",
    );

    /*---------------
     *  BUY LISTINGS
     *---------------*/
    $chart[] = array(
        'data'   => BuyListingQuery::getChartDatasetDataForItem($item),
        'label'  => "Buy Listings",
    );

    $wrap    = false;
    $content = json_encode($chart);

    return $content;
})
->assert('dataId',  '\d+')
->convert('dataId', $toInt);

/**
 * ----------------------
 *  route /status
 * ----------------------
 */
$app->get("/status", function() use($app) {
    ob_start();

    echo "there are [[ " . RequestSlotManager::getInstance()->getLength() . " ]] available slots right now \n";
    echo "there are still [[ " . WorkerQueueManager::getInstance()->getLength() . " ]] items in the queue \n";

    $content = ob_get_clean();

    return $app['twig']->render('status.html.twig', array(
        'dump' => $content,
    ));
});

/**
 * ----------------------
 *  route /search POST
 * ----------------------
 */
$app->post("/search", function (Request $request) use ($app) {
    $search = $request->get('search');
    return $app->handle(Request::create("/search/{$search}", 'GET'), HttpKernelInterface::SUB_REQUEST);
});

/**
 * ----------------------
 *  route /search GET
 * ----------------------
 */
$app->get("/search/{search}/{page}", function($search, $page) use($app) {
    if (!$search) {
        return $app->handle(Request::create("/searchform", 'GET'), HttpKernelInterface::SUB_REQUEST);
    }

    $itemsperpage = 10;
    $baseurl      = "/search/{$search}";
    $q = ItemQuery::create()
            ->filterByName("%{$search}%");

    $count    = $q->count();
    $lastpage = ceil($count / $itemsperpage);
    if ($page > $lastpage) {
        $page = $lastpage;
    }

    $items = $q->offset($itemsperpage * ($page - 1))
                    ->orderBy("Name", "ASC")
                    ->limit($itemsperpage)
                    ->find();

    return $app['twig']->render('type.html.twig', array(
        'page'     => $page,
        'lastpage' => $lastpage,
        'items'    => $items,
        'baseurl'  => $baseurl,
    ));
})
->assert('page',     '\d+')
->convert('page',    $toInt)
->convert('search',  function($search) { return urldecode($search); })
->value('search',    null)
->value('page',      1);

/**
 * ----------------------
 *  route /searchform
 * ----------------------
 */
$app->get("/searchform", function() use($app) {
    return $app['twig']->render('search.html.twig', array());
});

$app->run();

?>
