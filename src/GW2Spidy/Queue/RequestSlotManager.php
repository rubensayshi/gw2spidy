<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     *  gives us 1000 x (60 / (100 / 60)) = 36000 requests / hr = 10 requests / sec
     * this is excluding the time it takes to handle the slots
     *
     */
    protected function getSlots() {
        return 1000;
    }

    protected function getTimeout() {
        return 100;
    }

    /**
     * @return RequestSlotManager
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}

?>