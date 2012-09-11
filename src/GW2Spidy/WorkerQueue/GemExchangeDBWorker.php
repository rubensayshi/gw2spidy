<?php

namespace GW2Spidy\WorkerQueue;

use \DateTime;
use \DateTimeZone;
use \Exception;

use GW2Spidy\GemExchangeSpider;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

class GemExchangeDBWorker implements Worker {
    protected function getTypes() {
        return array(
            GemExchangeSpider::GEM_RATE_TYPE_RECIEVE_GEMS  => '\\GW2Spidy\\DB\\BuyGemRate',
            GemExchangeSpider::GEM_RATE_TYPE_RECIEVE_COINS => '\\GW2Spidy\\DB\\SellGemRate'
        );
    }

    public function getRetries() {
        return 1;
    }

    public function work(WorkerQueueItem $item) {
        $types = self::getTypes();
        if (!($type = $item->getData())) {
            throw new Exception("bad worker item - no type defined!");
        }
        if (!isset($types[$type]) || !($class = $types[$type])) {
            throw new Exception("bad worker item - type doesn't resolve to a class in the mapping!");
        }
        $queryClass = "{$class}Query";

        $data = GemExchangeSpider::getInstance()->getGemExchange($type);

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

            $exists = $queryClass::create()
                        ->filterByRateDatetime($date)
                        ->count() > 0;

            if (!$exists) {
                $new = new $class();
                $new->setAverage($copper)
                    ->setRateDatetime($date)
                    ->save();
            }
        }
    }

    public static function enqueueWorkers() {
        foreach (self::getTypes() as $type => $class) {
            self::enqueueWorker($type);
        }
    }

    public static function enqueueWorker($type) {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\GemExchangeDBWorker");
        $queueItem->setData($type);

        WorkerQueueManager::getInstance()->enqueue($queueItem);

        return $queueItem;
    }
}

?>