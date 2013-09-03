<?php

namespace GW2Spidy\NewQueue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     * 100 slots with 10 sec cooldown gives us :
     *     100 * (60 / (10 / 60)) = 36000 requests / hr = 10 requests / sec
     * 100 slots with 40 sec cooldown gives us :
     *     100 * (60 / (40 / 60)) =  9000 requests / hr = 2.5 requests / sec
     * 5 slots with 2 sec cooldown gives us :
     *     5 * (60 / (2 / 60)) =  9000 requests / hr = 2.5 requests / sec
     * this is excluding the time it takes to handle the slots
     *
     */
    protected function getSlots() {
        return getAppConfig("gw2spidy.request-slots.count");
    }

    protected function getTimeout() {
        return getAppConfig("gw2spidy.request-slots.cooldown");
    }

    public function getSlotsPerSecond() {
        return ($this->getSlots() / $this->getTimeout());
    }

    /**
     * @return RequestSlotManager
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}

