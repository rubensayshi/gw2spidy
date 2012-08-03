<?php

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemListingsDBWorker;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$item = ItemQuery::create()->findPK($argv[1]);

$worker = new ItemListingsDBWorker();
var_dump($worker->buildListingsDB($item));