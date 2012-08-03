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
$max     = in_array('--debug', $argv) ? 1 : 50;

$slotManager  = RequestSlotManager::getInstance();
$queueManager = WorkerQueueManager::getInstance();

// login here so our benchmarking per item ain't offset by it
print "login ... \n";
TradeMarket::getInstance()->doLogin();

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

        if (!in_array('--debug', $argv)) {
            print "no items, sleeping [60] ... \n";
            sleep(60);
        }

        $run++;
        continue;
    }

    echo "got item {$run} [".(microtime(true) - $begin)."] \n";

    // mark our slot as held
    $slot->hold();

    /*
     * process the item
     *  wrapped in trycatch to catch and log exceptions
     *  get a worker (reuse old instances) and let it work the item
     */
    try {
        $workerName = $queueItem->getWorker();

        if (!isset($workers[$workerName])) {
            $workers[$workerName] = new $workerName;
        }

        ob_start();
        $workers[$workerName]->work($queueItem);

        ob_get_clean();
    } catch (Exception $e) {
        $log = ob_get_clean();
        echo " !! worker process threw exception !! \n\n\n --------------- \n\n\n {$log} \n\n\n --------------- \n\n\n {$e} ";
    }

    echo "done [".(microtime(true) - $begin)."] \n";

    $run++;
}

