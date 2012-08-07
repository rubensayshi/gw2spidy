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

    /**
     * generate an MD5 hash based on processID, time and the provided $i
     *  to be used as the 'name' of a slot
     *
     * @param  int       $i
     * @return string
     */
    static public function getSlotName($i) {
        return md5(getmypid() . "::" . time() . "" . $i);
    }

    /**
     * setup the required amount of slots
     *  first truncate any old data
     *
     * @param  int        $slots        defaults to the configured amount
     */
    public function setup($slots = null) {
        $slots = $slots ?: $this->getSlots();

        $this->client->del($this->getSlotsQueueName());

        for ($i = 1; $i <= $slots; $i++) {
            $this->client->zadd($this->getSlotsQueueName(), 0, self::getSlotName($i));
        }
    }


    /**
     * check if we still have the required amount of slots
     *  if we have to many or to few we correct that
     *
     * counting is done within a transaction
     *  since we retrieve slots by popping them off the set, without this we'd always have to few ;)
     *
     * @param  int        $slots        defaults to the configured amount
     */
    public function check($slots = null) {
        $slots    = $slots ?: $this->getSlots();
        $queueKey = $this->getSlotsQueueName();

        // set a watch on the $queueKey
        $this->client->watch($queueKey);

        // start transaction
        $tx = $this->client->multi();

        // removed the slot from the $queueKey
        $this->client->zcard($queueKey);

        // execute the transaction
        $results = $this->client->exec();

        if ($results[0] > $slots) {
            $rem = $results[0] - $slots;
            echo "we need to remove [{$rem}] slots \n";

            $this->client->zremrangebyrank($queueKey, 0, $rem-1);
        } else if ($results[0] < $slots) {
            $add = $slots - $results[0];

            echo "we need to add [{$add}] slots \n";

            for ($i = 0; $i < $add; $i++) {
                $this->client->zadd($queueKey, 0, self::getSlotName($i));
            }
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