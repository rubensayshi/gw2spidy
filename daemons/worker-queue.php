<?php

use GW2Spidy\DB\WorkerQueueItemPeer;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();

for ($i = 0; $i < 10; $i++) {
    $sql = "SELECT
            *
        FROM `".WorkerQueueItemPeer::TABLE_NAME."`
        WHERE (`touched` IS NULL OR `touched` + `max_timeout` < NOW())
        AND   `status` <> 'DONE'
        ORDER BY `priority` DESC, `id` ASC
        LIMIT 1
        LOCK IN SHARE MODE";

    $con->beginTransaction();

    $prep = $con->prepare($sql);
    $prep->execute();

    $items = WorkerQueueItemPeer::populateObjects($prep);

    foreach ($items as $item) {
        $item->setHandlerUUID($UUID);
        $item->setTouched(new DateTime());
        $item->save();
    }

    $con->commit();

    foreach ($items as $item) {
        var_dump($item->getId());
        $workerName = $item->getWorker();

        if (!isset($workers[$workerName])) {
            $workers[$workerName] = new $workerName;
        }

        $workers[$workerName]->work($item);
        $item->setStatus('DONE');
    }
}