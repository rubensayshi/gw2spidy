<?php

namespace GW2Spidy\Util\RedisSlots;

use Predis\Client;

abstract class RedisSlotManager {
    protected $client;

    protected static $instance;

    private function __construct() {
        $this->client = new Client();
    }

    abstract protected function getSlotsQueueName();
    abstract protected function getSlots();
    abstract protected function getTimeout();

    /**
     * @return RedisSlotManager
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setup($slots = null) {
        $slots = $slots ?: $this->getSlots();

        $this->client->del($this->getSlotsQueueName());

        for ($i = 1; $i <= $slots; $i++) {
            $this->client->zadd($this->getSlotsQueueName(), 0, $i);
        }
    }

    public function getAvailableSlot() {
        $slots = $this->client->zrangeByScore($this->getSlotsQueueName(), 0, time(), array('limit' => array(0, 1)));
        $slot  = $slots ? $slots[0] : null;
        if (is_null($slot)) {
            return false;
        }

        $this->client->zrem($this->getSlotsQueueName(), $slot);

        return new RedisSlot($this, $slot);
    }

    public function release(RedisSlot $slot) {
        $this->client->zadd($this->getSlotsQueueName(), 0, $slot->getSlot());
    }

    public function hold(RedisSlot $slot) {
        $this->client->zadd($this->getSlotsQueueName(), time() + $this->getTimeout(), $slot->getSlot());
    }
}

?>