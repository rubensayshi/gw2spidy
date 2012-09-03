<?php


use GW2Spidy\TradeMarket;

use GW2Spidy\Queue\RequestSlotManager;
use GW2Spidy\Queue\WorkerQueueManager;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();
$run     = 0;
$max     = 100;
$debug   = in_array('--debug', $argv);

$slotManager  = RequestSlotManager::getInstance();
$queueManager = WorkerQueueManager::getInstance();

// login here so our benchmarking per item ain't offset by it
print "login ... \n";
try {
    TradeMarket::getInstance()->ensureLogin();
} catch (Exception $e) {
    echo "login failed ... sleeping [60] and restarting \n";
    sleep(60);
    exit(1);
}

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
while ($run < $max) {
    $begin = microtime(true);

    $slot = $slotManager->getAvailableSlot();

    if (!$slot) {
        print "no slots, sleeping [10] ... \n";
        sleep(10);

        continue;
    }

    echo "got slot, begin [".(microtime(true) - $begin)."] \n";

    $queueItem = $queueManager->next();

    /*
     * if we have no items
    *  sleep for a bit before trying again
    */
    if (!$queueItem) {
        // return the slot
        $slot->release();

        print "no items, sleeping [60] ... \n";
        sleep(60);

        $run++;
        continue;
    }

    echo "got item {$run} [".(microtime(true) - $begin)."] \n";

    // mark our slot as held
    $slot->hold();

    $workerName = $queueItem->getWorker();

    if (!isset($workers[$workerName])) {
        $workers[$workerName] = new $workerName;
    }

    $try = 1;
    $retries = $workers[$workerName]->getRetries();

    while (true) {
        /*
         * process the item
         *  wrapped in trycatch to catch and log exceptions
         *  get a worker (reuse old instances) and let it work the item
         */
        try {
            ob_start();
            $workers[$workerName]->work($queueItem);

            echo ($debug) ? ob_get_clean() : ob_clean();
            break;
        } catch (Exception $e) {
            $log = ob_get_clean();
            echo " !! worker process threw exception !! \n\n\n --------------- \n\n\n {$log} \n\n\n --------------- \n\n\n {$e} ";

            if ($try <= $retries) {
                echo "error, retrying, sleeping [5] ... \n";
                sleep(5);
                $try++;
                continue;
            } else {
                echo "error, sleeping [60] ... \n";
                sleep(60);
                break;
            }
        }
    }

    echo "done [".(microtime(true) - $begin)."] \n";

    $run++;
}

