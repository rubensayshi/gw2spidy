<?php

namespace GW2Spidy\WorkerQueue;

use \DateTime;
use \DateTimeZone;

use GW2Spidy\GemExchangeSpider;

use GW2Spidy\DB\GemExchange;
use GW2Spidy\DB\GemExchangeQuery;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

class GemExchangeDBWorker implements Worker {
    public function getRetries() {
        return 1;
    }

    public function work(WorkerQueueItem $item) {
        $data = GemExchangeSpider::getInstance()->getGemExchange();

        if (isset($data['plots']) && $data['plots']) {
            $plots = $data['plots'];
        } else if(isset($data['average'])) {
            $plots = array(array('average' => $data['average'], 'last_updated' => date('Y-m-d H:i:s')));
        }

        foreach ($plots as $plot) {
            /*
             * I'm not sure wtf ArenaNet is doing with the time here
             * but they use their server time or something and output it as if it's UTC (which it's not)
             * so I convert it to GMT+7, which seems to match (for now)
             *
             * and after that I convert it to our server timezone, until I convert everything to use UTC this will have to do
             */
            $date   = new DateTime($plot['last_updated']);
            $date   = new DateTime($date->format("Y-m-d H:i:s") . " GMT+7");
            $silver = $plot['average'];
            $copper = $silver * 100;
            $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

            $exists = GemExchangeQuery::create()
            ->filterByExchangeDate($date)
            ->filterByExchangeTime($date)
            ->count() > 0;

            if (!$exists) {
                $new = new GemExchange();
                $new->setAverage($copper)
                    ->setExchangeDate($date)
                    ->setExchangeTime($date)
                    ->save();
            }
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