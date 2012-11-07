<?php

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

foreach ($items as $dataId) {
    $cleaner = new ItemDatasetCleaner($dataId, ItemDatasetCleaner::TYPE_SELL_LISTING);
    $count = $cleaner->clean();
    unset($cleaner);

    echo "[{$dataId}] cleaned [{$count}] in ".mytime().", mem @ [".memory_get_usage(true)."] \n";
    @ob_flush();
}