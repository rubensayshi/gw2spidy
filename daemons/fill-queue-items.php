<?php

use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\Worker\ItemTypeDBWorker;
use GW2Spidy\Worker\ItemDBWorker;

require dirname(__FILE__) . '/../autoload.php';

/*
 * build ItemType DB
 */
ItemTypeDBWorker::enqueueWorker();

/*
 * build Item DB
 */
foreach (ItemTypeQuery::getAllTypes() as $type) {
    foreach ($type->getSubTypes() as $subtype) {
        ItemDBWorker::enqueueWorker($type, $subtype, true);
    }

    ItemDBWorker::enqueueWorker($type, null, true);
}