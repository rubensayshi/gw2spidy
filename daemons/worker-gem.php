<?php

use GW2Spidy\GW2SessionManager;

use \DateTime;
use \DateTimeZone;
use \Exception;

use GW2Spidy\DB\GoldToGemRate;
use GW2Spidy\DB\GoldToGemRateQuery;

use GW2Spidy\DB\GemToGoldRate;
use GW2Spidy\DB\GemToGoldRateQuery;

use GW2Spidy\GemExchangeSpider;
use GW2Spidy\NewQueue\RequestSlotManager;

require dirname(__FILE__) . '/../autoload.php';

$UUID    = getmypid() . "::" . time();
$con     = Propel::getConnection();
$run     = 0;
$max     = 100;
$debug   = in_array('--debug', $argv);

$slotManager  = RequestSlotManager::getInstance();

/*
 * login here, this allows us to exit right away on failure
 */
print "login ... \n";
try {
    $begin = microtime(true);
    $gw2session = GW2SessionManager::getInstance()->getSession();
    echo "login ok [".(microtime(true) - $begin)."] -> [".(int)$gw2session->getGameSession()."] -> [{$gw2session->getSessionKey()}] \n";

    GemExchangeSpider::getInstance()->setSession($gw2session);
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
        print "no slots, sleeping [2] ... \n";
        sleep(2);

        continue;
    }

    echo "got slot, begin [".(microtime(true) - $begin)."] \n";

    try {
        ob_start();

        $rates  = GemExchangeSpider::getInstance()->getGemExchangeRate();
        $volume = GemExchangeSpider::getInstance()->getGemExchangeVolume();

        if (!$rates || !$volume) {
            throw new Exception("No gem exchange data");
        }

        $date   = new DateTime();
        $date   = new DateTime($date->format('Y-m-d H:i:00'));

        if (!($exists = GoldToGemRateQuery::create()
                ->filterByRateDatetime($date)
                ->count() > 0)) {

            $goldtogem = new GoldToGemRate();
            $goldtogem->setRateDatetime($date);
            $goldtogem->setRate($rates['gold_to_gem'] * 100); // convert to copper
            $goldtogem->setVolume($volume['gem_count']);
            $goldtogem->save();
        }

        if (!($exists = GemToGoldRateQuery::create()
                        ->filterByRateDatetime($date)
                        ->count() > 0)) {

            $goldtogem = new GemToGoldRate();
            $goldtogem->setRateDatetime($date);
            $goldtogem->setRate($rates['gem_to_gold'] * 100); // convert to copper
            $goldtogem->setVolume($volume['gold_count']);
            $goldtogem->save();
        }

        if ($debug) {
            echo ob_get_clean();
        } else {
            ob_get_clean();
        }
    } catch (Exception $e) {
        $log = ob_get_clean();
        echo " --------------- \n !! worker process threw exception !!\n --------------- \n {$log} \n --------------- \n {$e} \n --------------- \n";

        echo "error, sleeping [60] ... \n";
        sleep(60);
        break;
    }

    echo "done [".(microtime(true) - $begin)."] \n";

    echo "sleeping [180] ... \n";
    sleep(180);

    $run++;
}

