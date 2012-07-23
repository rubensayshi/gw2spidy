<?php

use GW2Spidy\DB\ItemQuery;
use GW2Spidy\Application;
use GW2Spidy\DB\ListingQuery;
use GW2Spidy\DB\ListingPeer;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

require dirname(__FILE__) . '/config/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

$app  = Application::getInstance();
$wrap = true;

if ($_GET['act'] == 'item') {
    if (isset($_GET['id']) && (string)(int)(string)$_GET['id'] === (string)$_GET['id']) {
        $id = (int)(string)$_GET['id'];
    } else {
        $id = 4016;
    }

    $item = ItemQuery::create()->findPK($id);

    $content = $app->render("item", array(
        'item' => $item,
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

    if ($item->getListings()->count()) {
        $c = new Criteria();

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
    }

    $chart[] = $dataset;

    $wrap    = false;
    $content = json_encode($chart);
} else {
    $ids = array(4016);

    $items = ItemQuery::create()->findPks($ids);

    $content = $app->render("index", array(
        'items' => $items,
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