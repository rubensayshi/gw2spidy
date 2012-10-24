<?php

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\Dataset\DatasetManager;
use GW2Spidy\Dataset\GemExchangeDataset;
use GW2Spidy\Dataset\ItemVolumeDataset;
use GW2Spidy\Dataset\ItemDataset;

require dirname(__FILE__) . '/../autoload.php';

$t = microtime(true);
function mytime() {
    $r = (microtime(true) - $GLOBALS['t']);
    $GLOBALS['t'] = microtime(true);

    return $r;
}

$modnum = null;
$modmod = null;
if (isset($argv[1], $argv[2])) {
    $modnum = $argv[1];
    $modmod = $argv[2];
}

$dm = DatasetManager::getInstance();

$q = ItemQuery::create();
$q->where("data_id % {$modmod} = {$modnum}");

var_dump(mytime());

if (!is_null($modnum) && $modnum != 0) {
    do {
        echo "[[ ".mytime()." ]] [[ TYPE_GEM_TO_GOLD ]] \n";
        $ds = $dm->getGemDataset(GemExchangeDataset::TYPE_GEM_TO_GOLD);
    } while (!$ds->uptodate);

    do {
        echo "[[ ".mytime()." ]] [[ TYPE_GOLD_TO_GEM ]] \n";
        $ds = $dm->getGemDataset(GemExchangeDataset::TYPE_GOLD_TO_GEM);
    } while (!$ds->uptodate);
}

foreach ($q->find() as $item) {
    do {
        echo "[[ ".mytime()." ]] [[ {$item->getDataId()} ]] [[ TYPE_SELL_LISTING ]] \n";
        $ds = $dm->getItemDataset($item, ItemDataset::TYPE_SELL_LISTING);
    } while (!$ds->uptodate);
    do {
        echo "[[ ".mytime()." ]] [[ {$item->getDataId()} ]] [[ TYPE_BUY_LISTING ]] \n";
        $ds = $dm->getItemDataset($item, ItemDataset::TYPE_BUY_LISTING);
    } while (!$ds->uptodate);
    do {
        echo "[[ ".mytime()." ]] [[ {$item->getDataId()} ]] [[ VOLUME ]] [[ TYPE_SELL_LISTING ]] \n";
        $ds = $dm->getItemVolumeDataset($item, ItemVolumeDataset::TYPE_SELL_LISTING);
    } while (!$ds->uptodate);
    do {
        echo "[[ ".mytime()." ]] [[ {$item->getDataId()} ]] [[ VOLUME ]] [[ TYPE_BUY_LISTING ]] \n";
        $ds = $dm->getItemVolumeDataset($item, ItemVolumeDataset::TYPE_BUY_LISTING);
    } while (!$ds->uptodate);
}

