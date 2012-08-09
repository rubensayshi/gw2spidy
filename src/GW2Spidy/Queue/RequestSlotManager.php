<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     * 1400 requests with a (250 sec = ) 4.666 min timeout
     *  gives us 1400 x (60 / (250 / 60)) = 201600 requests / hr = 2.8 requests / sec
     * this is excluding the time it takes to handle the slots
     *
     * in 1 round fill-queue-hourly we create about 6500 jobs
     */
    protected function getSlots() {
        return 1400;
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