<?php

use GW2Spidy\Queue\WorkerQueueItem;
use GW2Spidy\WorkerQueue\GemExchangeDBWorker;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$worker = new GemExchangeDBWorker();
var_dump($worker->work(new WorkerQueueItem()));