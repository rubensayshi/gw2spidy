<?php

/**
 * process queue items
 */

use GW2Spidy\Worker\ItemDBWorker;
use GW2Spidy\GemExchangeSpider;

use GW2Spidy\GW2SessionManager;

use GW2Spidy\TradingPostSpider;

use GW2Spidy\Queue\RequestSlotManager;
use GW2Spidy\Queue\QueueManager;


require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$workers = array();
$con     = Propel::getConnection();
$run     = 0;
$max     = 100;
$debug   = in_array('--debug', $argv);

if ($debug || (defined('SQL_LOG_MODE') && SQL_LOG_MODE)) {
    $con->setLogLevel(\Propel::LOG_DEBUG);
    $con->useDebug(true);
}

$slotManager  = RequestSlotManager::getInstance();
$queueManager = QueueManager::getInstance()->getItemQueueManager();

/*
 * login here, this allows us to exit right away on failure
 */
print "login ... \n";
try {
    $begin = microtime(true);
    $gw2session = GW2SessionManager::getInstance()->getSession();
    echo "login ok [".(microtime(true) - $begin)."] -> [".(int)$gw2session->getGameSession()."] -> [{$gw2session->getSessionKey()}] \n";

    GemExchangeSpider::getInstance()->setSession($gw2session);
    TradingPostSpider::getInstance()->setSession($gw2session);
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

            if ($debug) {
                echo ob_get_clean();
            } else {
                ob_get_clean();
            }

            break;
        } catch (Exception $e) {
            $log = ob_get_clean();
            echo " --------------- \n !! worker process threw exception !!\n --------------- \n {$log} \n --------------- \n {$e} \n --------------- \n";

           if ($e->getCode() == ItemDBWorker::ERROR_CODE_NO_LONGER_EXISTS || strstr("CurlRequest failed [[ 401 ]]", $e->getMessage())) {
                break;
            }

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

