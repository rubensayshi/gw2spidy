<?php

use GW2Spidy\DB\ListingQuery;

use GW2Spidy\DB\ListingPeer;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

require dirname(__FILE__) . '/config/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
}

if (!isset($_GET['id']) || (string)(int)(string)$_GET['id'] !== (string)$_GET['id']) {
    throw new Exception('invalid request');
}

$item = ItemQuery::create()->findPk((int)(string)$_GET['id']);

if (!$item || !($item instanceof Item)) {
    throw new Exception('invalid request');
}

$data = array();

if ($item->getListings()->count()) {
    $c = new Criteria();
    $c->addGroupByColumn(ListingPeer::ITEM_ID);
    $c->addGroupByColumn(ListingPeer::LISTING_DATE);
    $c->addSelectColumn(ListingPeer::LISTING_DATE);
    $c->addSelectColumn("SUM(unit_price * quantity) / SUM(quantity) as AVG_UNIT_PRICE");
    $c->add(ListingPeer::ITEM_ID, $item->getDataId());

    foreach (BasePeer::doSelect($c)->fetchAll(PDO::FETCH_ASSOC) as $listingDayAvg) {
        $data[] = array($listingDayAvg['LISTING_DATE'], $listingDayAvg['AVG_UNIT_PRICE']);
    }
}

echo json_encode($data);