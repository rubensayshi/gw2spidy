<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     * 650 requests with a (300 sec = ) 5min timeout
     *  gives us 650 x (60 / (300 / 60)) = 7800 requests / hr = 2.166 requests / sec
     *
     * in 1 hour we create about 6500 jobs, our total requests / hr should exceed this by at least 10% to be able to catch up
     */
    protected function getSlots() {
        return 650;
    }

    protected function getTimeout() {
        return 300;
    }

    /**
     * @return RequestSlotManager
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}

?>