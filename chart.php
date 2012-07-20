<?php

use GW2Spidy\DB\ListingQuery;

use GW2Spidy\DB\ListingPeer;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

require dirname(__FILE__) . '/config/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

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
    $c->addGroupByColumn(ListingPeer::ITEM_ID);
    $c->addGroupByColumn(ListingPeer::LISTING_DATE);
    $c->addSelectColumn(ListingPeer::LISTING_DATE);
    $c->addSelectColumn(ListingPeer::LISTING_TIME);
    $c->addSelectColumn("SUM(unit_price * quantity) / SUM(quantity) as AVG_UNIT_PRICE");
    $c->add(ListingPeer::ITEM_ID, $item->getDataId());

    foreach (BasePeer::doSelect($c)->fetchAll(PDO::FETCH_ASSOC) as $listingDayAvg) {
        $date = new DateTime("{$listingDayAvg['LISTING_DATE']} {$listingDayAvg['LISTING_TIME']}");
        $dataset[] = array($date->getTimestamp()*1000, $listingDayAvg['AVG_UNIT_PRICE']);
    }
}

$chart[] = $dataset;

echo json_encode($chart);