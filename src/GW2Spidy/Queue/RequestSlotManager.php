<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     * 700 requests with a (250 sec = ) 4.666 min timeout
     *  gives us 700 x (60 / (250 / 60)) = 10080 requests / hr = 2.8 requests / sec
     * this is excluding the time it takes to handle the slots
     *
     * in 1 hour we create about 6500 jobs, our total requests / hr should exceed this by at least 25% to be able to catch up
     */
    protected function getSlots() {
        return 700;
    }

    protected function getTimeout() {
        return 250;
    }

    /**
     * @return RequestSlotManager
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}

?>