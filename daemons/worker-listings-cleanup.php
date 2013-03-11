<?php

use GW2Spidy\Dataset\GemDatasetCleaner;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\Dataset\ItemDatasetCleaner;

require dirname(__FILE__) . '/../autoload.php';

$t = microtime(true);
function mytime() {
    $r = (microtime(true) - $GLOBALS['t']);
    $GLOBALS['t'] = microtime(true);

    return $r;
}

$q = ItemQuery::create()->select('dataId');
if (isset($argv[1])) {
    $q->filterByDataId($argv[1]);
}

$items = $q->find();

var_dump(mytime());

$cleaner = new GemDatasetCleaner(GemDatasetCleaner::TYPE_GEM_TO_GOLD);
$countM = $cleaner->clean(ItemDatasetCleaner::CLEANUP_MONTH);
$countW = $cleaner->clean(ItemDatasetCleaner::CLEANUP_WEEK);
unset($cleaner);

echo "[GEMS][GEM_TO_GOLD] cleaned [{$countM}] > month old and [{$countW}] > week old hours in ".mytime().", mem @ [".memory_get_usage(true)."] \n";
@ob_flush();

$cleaner = new GemDatasetCleaner(GemDatasetCleaner::TYPE_GOLD_TO_GEM);
$countM = $cleaner->clean(ItemDatasetCleaner::CLEANUP_MONTH);
$countW = $cleaner->clean(ItemDatasetCleaner::CLEANUP_WEEK);
unset($cleaner);

echo "[GEMS][GOLD_TO_GEM] cleaned [{$countM}] > month old and [{$countW}] > week old hours in ".mytime().", mem @ [".memory_get_usage(true)."] \n";
@ob_flush();

foreach ($items as $dataId) {
    $cleaner = new ItemDatasetCleaner($dataId, ItemDatasetCleaner::TYPE_SELL_LISTING);
    $countM = $cleaner->clean(ItemDatasetCleaner::CLEANUP_MONTH);
    $countW = $cleaner->clean(ItemDatasetCleaner::CLEANUP_WEEK);
    unset($cleaner);

    echo "[{$dataId}][sell] cleaned [{$countM}] > month old and [{$countW}] > week old hours in ".mytime().", mem @ [".memory_get_usage(true)."] \n";
    @ob_flush();

    $cleaner = new ItemDatasetCleaner($dataId, ItemDatasetCleaner::TYPE_BUY_LISTING);
    $countM = $cleaner->clean(ItemDatasetCleaner::CLEANUP_MONTH);
    $countW = $cleaner->clean(ItemDatasetCleaner::CLEANUP_WEEK);
    unset($cleaner);

    echo "[{$dataId}][buy] cleaned [{$countM}] > month old and [{$countW}] > week old hours in ".mytime().", mem @ [".memory_get_usage(true)."] \n";
    @ob_flush();
}