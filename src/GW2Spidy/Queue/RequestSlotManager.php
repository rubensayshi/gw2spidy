<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisSlots\RedisSlotManager;

class RequestSlotManager extends RedisSlotManager {
    protected function getSlotsQueueName() {
        return 'request.slots';
    }
    /**
     *  gives us 800 x (60 / (200 / 60)) = 14400 requests / hr = 4 requests / sec
     * this is excluding the time it takes to handle the slots
     *
     */
    protected function getSlots() {
        return 800;
    }

    protected function getTimeout() {
        return 200;
    }

    /**
     * @return RequestSlotManager
     */
    public static function getInstance() {
        return parent::getInstance();
    }
}

?>