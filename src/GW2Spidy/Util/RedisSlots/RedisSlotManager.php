<?php

namespace GW2Spidy\Util\RedisSlots;

use Predis\Client;

abstract class RedisSlotManager {
    protected $client;

    protected static $instance;

    protected function __construct() {
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
        $queueKey  = $this->getSlotsQueueName();
        $triesLeft = 2;

        do {
            // set a watch on the $queueKey
            $this->client->watch($queueKey);

            // pop the hotest slot off $queueKey which is between 0 and time() with limit 0,1
            $slots = $this->client->zrangeByScore($queueKey, 0, time(), array('limit' => array(0, 1)));
            // grab the slot we popped off
            $slot  = $slots ? $slots[0] : null;

            // no slot :(
            if (is_null($slot)) {
                return null;
            }

            // start transaction
            $tx = $this->client->multi();

            // removed the slot from the $queueKey
            $this->client->zrem($queueKey, $slot);

            // execute the transaction
            $results = $this->client->exec();

            // check if the zrem command removed 1 (or more)
            // if it did we can use this slot
            if ($results[0] >= 1) {
                return new RedisSlot($this, $slot);
            }

            // if we didn't get a usable slot we retry
        } while ($triesLeft-- > 0);

        return null;
    }

    public function release(RedisSlot $slot) {
        $this->client->zadd($this->getSlotsQueueName(), 0, $slot->getSlot());
    }

    public function hold(RedisSlot $slot) {
        $this->client->zadd($this->getSlotsQueueName(), time() + $this->getTimeout(), $slot->getSlot());
    }

    public function getLength() {
        return $this->client->zcount($this->getSlotsQueueName(), 0, time());
    }
}

?>