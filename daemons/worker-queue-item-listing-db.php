<?php

/**
 * process queue items
 */

use GW2Spidy\GW2SessionManager;

use GW2Spidy\TradingPostSpider;

use GW2Spidy\NewQueue\ItemDBQueueWorker;
use GW2Spidy\NewQueue\RequestSlotManager;
use GW2Spidy\NewQueue\ItemListingDBQueueManager;
use GW2Spidy\NewQueue\ItemListingDBQueueWorker;

define('METHOD_SEARCH_JSON', 'search.json');
define('METHOD_LISTINGS_JSON', 'listings.json');

require dirname(__FILE__) . '/../autoload.php';

function logg($msg){ 
    echo "[" . date("Y-m-d H:i:s") . "] " . $msg;
}

$con     = Propel::getConnection();
$debug   = in_array('--debug', $argv);

if ($debug || (defined('SQL_LOG_MODE') && SQL_LOG_MODE)) {
    $con->setLogLevel(\Propel::LOG_DEBUG);
    $con->useDebug(true);
}

$slotManager  = RequestSlotManager::getInstance();
$queueManager = new ItemListingDBQueueManager();
$queueWorker  = new ItemListingDBQueueWorker($queueManager);

/*
 * login here, this allows us to exit right away on failure
 */
logg("login ...\n");
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
 * determine if we're crawling the item listings with search.json or listings.json
 */
if (!getAppConfig("gw2spidy.use_shroud_magic") && getAppConfig("gw2spidy.use_listings-json") && $gw2session->getGameSession()) {
    $method = METHOD_LISTINGS_JSON;
} else {
    $method = METHOD_SEARCH_JSON;
}

/*
 * $run up to $max in 1 process, then exit so process gets revived
 *  this is to avoid any memory problems (propel keeps a lot of stuff in memory)
 */
$run = 0;
$max = 100;
while ($run < $max) {

    $slot = $slotManager->getAvailableSlot();

    if (!$slot) {
        logg("no slots, sleeping [9.5] ... \n");
        usleep(9.5 * 1000 * 1000);

        continue;
    }

    logg("got slot, begin ...");

    if ($method == METHOD_LISTINGS_JSON) {
        $workload = $queueManager->next();
    } else {
        $workload = array();
        for ($i = 0; $i < getAppConfig("gw2spidy.items-per-request"); $i++) {
            if ($queueItem = $queueManager->next()) {
                $workload[] = $queueItem;
            }
        }
    }

    /*
     * if we have no items
    *  sleep for a bit before trying again
    */
    if (!$workload) {
        // return the slot
        $slot->release();

        logg("no items, sleeping [60] ... \n");
        sleep(60);

        $run++;
        continue;
    }

    echo(" got item {$run} ...");

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

            $queueWorker->work($workload);

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

    echo(" done.\n");

    $run++;
}

