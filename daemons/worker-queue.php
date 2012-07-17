<?php

use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\WorkerQueueItemPeer;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();

/*
 * we loop 50 times to accept a total of 50 queue items in 1 process,
 *  after that we let our process exit to avoid memory problems etc
 */
for ($i = 0; $i < 50; $i++) {
    /*
     * query with LOCK IN SHARE MODE for the highest priority, oldest item
     *  which hasn't been touched yet, or was touched before it's max timeout
     *  and isn't done already (ofcourse)
     */
    $sql = "SELECT
            *
        FROM `".WorkerQueueItemPeer::TABLE_NAME."`
        WHERE (`touched` IS NULL OR `touched` + `max_timeout` < NOW())
        AND   `status` <> 'DONE'
        ORDER BY `priority` DESC, `id` ASC
        LIMIT 1
        LOCK IN SHARE MODE";

    // START TRANSACTION
    $con->beginTransaction();

    $prep = $con->prepare($sql);
    $prep->execute();

    $items = WorkerQueueItemPeer::populateObjects($prep);

    /*
     * if we have no items
     *  close the transaction
     *  sleep for a bit before trying again
     */
    if (count($items) == 0) {
        // CLOSE TRANSACTION
        $con->commit();

        if (!in_array('--dev', $argv)) {
            print "no items, sleeping ... \n";
            sleep(60);
        }

        continue;
    /*
     * if we do have items
     *  mark them touched and with our UUID
     *  then close the transaction
     */
    } else {
        foreach ($items as $item) {
            $item->setHandlerUUID($UUID);
            $item->setTouched(new DateTime());
            $item->save();
        }

        // CLOSE TRANSACTION
        $con->commit();
    }


    /*
     * process the items
     *  wrapped in trycatch to catch and log exceptions
     *  get a worker (reuse old instances) and let it work the item
     *
     * mark done/error and add log
     * finish with touching it one final time and saving
     */
    foreach ($items as $item) {
        $done = false;

        try {
            $workerName = $item->getWorker();

            if (!isset($workers[$workerName])) {
                $workers[$workerName] = new $workerName;
            }

            ob_start();
            $workers[$workerName]->work($item);

            $done = true;
        } catch (Exception $e) {
            $log = ob_get_clean();
            $item->setStatus('ERROR');
            $item->setLastLog("{$log} \n\n\n --------------- \n\n\n {$e}");

            echo " !! worker process threw exception !! \n\n\n --------------- \n\n\n {$log} \n\n\n --------------- \n\n\n {$e} ";
        }

        if ($done) {
            $item->setStatus('DONE');
            $item->setLastLog(ob_get_clean());
        }

        $item->setTouched(new DateTime());
        $item->save();
    }

    var_dump(memory_get_usage());
}

/*
 * clean up all items marked as 'DONE' and older then 12 hours
 */
$query = WorkerQueueItemQuery::create();
$query->add(WorkerQueueItemPeer::TOUCHED, (time() + 12 * 3600), Criteria::GREATER_THAN)
      ->add(WorkerQueueItemPeer::STATUS, 'DONE');
foreach ($query->find() as $item) {
    $item->delete();
}