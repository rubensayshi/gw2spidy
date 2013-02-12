<?php

/**
 * process queue items
 */

use GW2Spidy\GW2SessionManager;

use GW2Spidy\TradingPostSpider;

use GW2Spidy\NewQueue\RequestSlotManager;
use GW2Spidy\NewQueue\ItemDBQueueManager;
use GW2Spidy\NewQueue\ItemDBQueueWorker;


require dirname(__FILE__) . '/../autoload.php';

function logg($msg){ 
    echo "[" . date("Y-m-d H:i:s") . "] " . $msg;
}

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
$queueManager = new ItemDBQueueManager();
$queueWorker  = new ItemDBQueueWorker($queueManager);

/*
 * login here, this allows us to exit right away on failure
 */
print "login ... \n";
try {
    $gw2session = GW2SessionManager::getInstance()->getSession();
    logg("login ok -> [".(int)$gw2session->getGameSession()."] -> [{$gw2session->getSessionKey()}] \n");

    TradingPostSpider::getInstance()->setSession($gw2session);
} catch (Exception $e) {
    logg("login failed ... sleeping [60] and restarting \n");
    sleep(60);
    exit(1);
}

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
while ($run < $max) {
    $slot = $slotManager->getAvailableSlot();

    if (!$slot) {
        logg("no slots, sleeping [4.5] ... \n");
        usleep(4.5 * 1000 * 1000);

        continue;
    }

    logg("got slot, begin \n");

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

    logg("got item {$run} \n");

    // mark our slot as held
    $slot->hold();

    $try = 1;
    $retries = 2;

    while (true) {
        /*
         * process the item
         *  wrapped in trycatch to catch and log exceptions
         *  get a worker (reuse old instances) and let it work the item
         */
        try {
            ob_start();
            $queueWorker->work($queueItem);

            if ($debug) {
                echo ob_get_clean();
            } else {
                ob_get_clean();
            }

            break;
        } catch (Exception $e) {
            $log = ob_get_clean();
            echo " --------------- \n !! worker process threw exception !!\n --------------- \n {$log} \n --------------- \n {$e} \n --------------- \n";

           if ($e->getCode() == ItemDBQueueWorker::ERROR_CODE_NO_LONGER_EXISTS || strstr("CurlRequest failed [[ 401 ]]", $e->getMessage())) {
                break;
            }

            if ($try <= $retries) {
                logg("error, retrying, sleeping [5] ... \n");
                sleep(5);
                $try++;
                continue;
            } else {
                logg("error, sleeping [60] ... \n");
                sleep(60);
                break;
            }
        }
    }

    logg("done \n");

    $run++;
}

