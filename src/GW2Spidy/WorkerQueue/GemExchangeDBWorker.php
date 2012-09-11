<?php

namespace GW2Spidy\WorkerQueue;

use \DateTime;
use \DateTimeZone;
use \Exception;

use GW2Spidy\DB\GoldToGemRate;
use GW2Spidy\DB\GoldToGemRateQuery;

use GW2Spidy\DB\GemToGoldRate;
use GW2Spidy\DB\GemToGoldRateQuery;

use GW2Spidy\GemExchangeSpider;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

class GemExchangeDBWorker implements Worker {
    public function getRetries() {
        return 1;
    }

    public function work(WorkerQueueItem $item) {
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
            $goldtogem->setRate($rates['gold_to_gem'] * 100); // convert to copper
            $goldtogem->setVolume($volume['gems']);
            $goldtogem->save();
        }

        if (!($exists = GemToGoldRateQuery::create()
                        ->filterByRateDatetime($date)
                        ->count() > 0)) {

            $goldtogem = new GemToGoldRate();
            $goldtogem->setRate($rates['gold_to_gem'] * 100); // convert to copper
            $goldtogem->setVolume($volume['gems']);
            $goldtogem->save();
        }
    }

    public static function enqueueWorker() {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\GemExchangeDBWorker");

        WorkerQueueManager::getInstance()->enqueue($queueItem);

        return $queueItem;
    }
}

?>