<?php

use GW2Spidy\Application;

use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ListingQuery;
use GW2Spidy\DB\ListingPeer;
use GW2Spidy\DB\Item;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$app  = Application::getInstance();
0 && $app->debugSQL();
$wrap = true;

if ($app->isCLI()) {
    if (isset($argv[1])) {
        $_GET['act'] = $argv[1];
    }
}

if (!isset($_GET['act'])) {
    $_GET['act'] = 'index';
}

if ($_GET['act'] == 'item') {
    if (isset($_GET['id']) && (string)(int)(string)$_GET['id'] === (string)$_GET['id']) {
        $id = (int)(string)$_GET['id'];
    } else {
        throw new \Exception("Item not found");
    }

    $item = ItemQuery::create()->findPK($id);

    if (!$item) {
        throw new \Exception("Item not found");
    }


    $content = $app->render("item", array(
            'item' => $item,
    ));

} else if ($_GET['act'] == 'type') {
    $itemsperpage = 25;
    $baseurl      = "index.php?act=type";

    if (isset($_GET['type']) && (string)(int)(string)$_GET['type'] === (string)$_GET['type']) {
        $type = (int)(string)$_GET['type'];
    } else {
        throw new \Exception("Item not found");
    }
    if (!isset($_GET['subtype'])) {
        $subtype = null;
    } else if((string)(int)(string)$_GET['subtype'] === (string)$_GET['subtype']) {
        $subtype = (int)(string)$_GET['subtype'];
    } else {
        throw new \Exception("Item not found");
    }
    if (!isset($_GET['page'])) {
        $page = 1;
    } else if((string)(int)(string)$_GET['page'] === (string)$_GET['page']) {
        $page = (int)(string)$_GET['page'];
    } else {
        throw new \Exception("Item not found");
    }

    $q = ItemQuery::create();

    if (!is_null($type)) {
        $baseurl = "{$baseurl}&type={$type}";
        $q->filterByItemTypeId($type);
    }
    if (!is_null($subtype)) {
        $baseurl = "{$baseurl}&subtype={$subtype}";
        $q->filterByItemSubTypeId($subtype);
    }

    $count    = $q->count();
    $lastpage = ceil($count / $itemsperpage);
    if ($page > $lastpage) {
        $page = $lastpage;
    }

    $items = $q->offset($itemsperpage * $page)
               ->limit($itemsperpage)
               ->find();

    $content = $app->render("items", array(
        'page'     => $page,
        'lastpage' => $lastpage,
        'items'    => $items,
        'baseurl'  => $baseurl,
    ));

} else if ($_GET['act'] == 'chart') {
    if (!isset($_GET['id']) || (string)(int)(string)$_GET['id'] !== (string)$_GET['id']) {
        throw new Exception('invalid request');
    }

    $item = ItemQuery::create()->findPk((int)(string)$_GET['id']);

    if (!$item || !($item instanceof Item)) {
        throw new Exception('invalid request');
    }

    $chart   = array();
    $dataset = array();

    $res = ListingQuery::create()
            ->groupByItemId()
            ->groupByListingDate()
            ->groupByListingTime()
            ->select(array('id', 'listingdate', 'listingtime'))
            ->withColumn('SUM(unit_price * quantity) / SUM(quantity)', 'avgunitprice')
            ->orderByListingDate('asc')
            ->orderByListingTime('asc')
            ->filterByItemId($item->getDataId())
            ->find();

    foreach ($res as $listingEntry) {
        $date = new DateTime("{$listingEntry['listingdate']} {$listingEntry['listingtime']}");

        $listingEntry['avgunitprice'] = round($listingEntry['avgunitprice'], 2);

        $dataset[] = array($date->getTimestamp()*1000, $listingEntry['avgunitprice']);
    }

    $chart[] = $dataset;

    $wrap    = false;
    $content = json_encode($chart);
} else if ($_GET['act'] == 'status') {
        $res = WorkerQueueItemQuery::create()
                ->withColumn('COUNT(*)', 'Count')
                ->select(array('Status', 'Count'))
                ->groupByStatus()
                ->find();

        ob_start();
        foreach ($res as $statusCount) {
            var_dump($statusCount);
        }

        $content = "<pre>".ob_get_clean()."</pre>";
} else {
    $types = ItemTypeQuery::create()
                    ->orderByTitle()
                    ->find();

    $content = $app->render("index", array(
        'types' => $types,
    ));
}

if ($wrap) {
    echo $app->render("wrapper", array(
        'content' => $content
    ));
} else {
    echo $content;
}

?>