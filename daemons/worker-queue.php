<?php

use GW2Spidy\DB\RequestFloodControlPeer;

use GW2Spidy\DB\RequestFloodControlQuery;

use GW2Spidy\DB\WorkerQueueItemQuery;
use GW2Spidy\DB\WorkerQueueItemPeer;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();
$run     = 0;
$max     = in_array('--debug', $argv) ? 1 : 50;
$slottime= SLOT_TIMEOUT . "sec";

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
while ($run < $max) {
    /*
     * query with LOCK FOR UPDATE for the oldest request_flood_control slot
     */
    $sql = "SELECT
            *
        FROM `".RequestFloodControlPeer::TABLE_NAME."`
        ORDER BY `touched` ASC, `id` ASC
        LIMIT 1
        FOR UPDATE";

    // START TRANSACTION
    $con->beginTransaction();

    $begin = microtime(true);
    echo "begin \n";

    $prep = $con->prepare($sql);
    $prep->execute();

    /*
     * if there are no slots then this is development, FFA do whatever you want ;)
     *  otherwise we grab the first (should be the only)
     */
    $slots = RequestFloodControlPeer::populateObjects($prep);
    if (!count($slots)) {
        // CLOSE TRANSACTION
        echo "commit [".__LINE__."] [".(microtime(true) - $begin)."] \n";
        $con->commit();

        throw new Exception("No RequestFoodControl setup");
    } else {
        $slot = $slots[0];
    }

    /*
     * check if we have a slot which we're allowed to use (within the timebox)
     *  if not then we sleep until that slot would be available
     *
     *  if the slot is ok, we touch it and we can run
     */
    if ($slot->getTouched('U') > strtotime("-{$slottime}")) {
        // CLOSE TRANSACTION
        echo "commit [".__LINE__."] [".(microtime(true) - $begin)."] \n";
        $con->commit();

        $sleep = $slot->getTouched('U') - strtotime("-{$slottime}");
        print "no slots, sleeping [{$sleep}] ... \n";
        sleep($sleep);

        continue;
    }

    echo "got slot [".(microtime(true) - $begin)."] \n";

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

        $slot->setTouched(new DateTime());
        $slot->save();

        // CLOSE TRANSACTION
        echo "commit [".__LINE__."] [".(microtime(true) - $begin)."] \n";
        $con->commit();
    }

    echo "got item {$run} [".(microtime(true) - $begin)."] \n";

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
