<?php

use GW2Spidy\Queue\RequestSlotManager;
use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\WorkerQueueItemPeer;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();
$run     = 0;
$max     = in_array('--debug', $argv) ? 1 : 50;

$slotManager = RequestSlotManager::getInstance();

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
while ($run < $max) {
    $slot = $slotManager->getAvailableSlot();

    if (!$slot) {
        print "no slots, sleeping [10] ... \n";
        sleep(10);

        continue;
    }

    // START TRANSACTION
    $con->beginTransaction();
    $begin = microtime(true);

    echo "got slot, begin \n";

    /*
     * query with LOCK FOR UPDATE for the highest priority, oldest item
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
        FOR UPDATE";

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
        echo "commit [".__LINE__."] [".(microtime(true) - $begin)."] \n";
        $con->commit();

        $slot->release();

        if (!in_array('--debug', $argv)) {
            print "no items, sleeping [60] ... \n";
            sleep(60);
        }

        $run++;
        continue;
    /*
     * if we do have items
     *  mark them touched and with our UUID
     *  then close the transaction
     *
     * also touch our slot
     */
    } else {
        $item = $items[0];

        $item->setHandlerUUID($UUID);
        $item->setTouched(new DateTime());
        $item->save();

        // CLOSE TRANSACTION
        echo "commit [".__LINE__."] [".(microtime(true) - $begin)."] \n";
        $con->commit();
    }

    echo "got item {$run} [".(microtime(true) - $begin)."] \n";

    // CLOSE CONNECTION - should already be closed, just to be sure
    $con->commit();

    $slot->hold();

    /*
     * process the item
     *  wrapped in trycatch to catch and log exceptions
     *  get a worker (reuse old instances) and let it work the item
     *
     * mark done/error and add log
     * finish with touching it one final time and saving
     */

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
        $log = ob_get_clean();
        $item->setStatus('DONE');
        $item->setLastLog($log);

        if (in_array('--debug', $argv)) {
            echo $log;
        }
    }

    $item->setTouched(new DateTime());
    $item->save();
    $run++;
}

/*
 * clean up all items marked as 'DONE' and older then 12 hours
 */
$query = WorkerQueueItemQuery::create();
$query->add(WorkerQueueItemPeer::TOUCHED, (time() + (1 * 3600)), Criteria::LESS_THAN)
      ->add(WorkerQueueItemPeer::STATUS, 'DONE');
$query->delete();
