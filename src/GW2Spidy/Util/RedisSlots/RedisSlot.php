<?php

namespace GW2Spidy\Util\RedisSlots;

class RedisSlot {
    protected $slot;
    protected $manager;
    protected $state;

    const STATE_NEW      = 'new';
    const STATE_HELD     = 'held';
    const STATE_RELEASED = 'released';

    public function __construct($manager, $slot, $state = null) {
        $this->manager = $manager;
        $this->slot    = $slot;
        $this->state   = $state ?: self::STATE_NEW;
    }

    public function __destruct() {
        if ($this->state == self::STATE_NEW) {
            $this->hold();
        }
    }

    public function hold() {
        $this->state = self::STATE_HELD;

        $this->manager->hold($this);
    }

    public function release() {
        $this->state = self::STATE_RELEASED;

        $this->manager->release($this);
    }

    public function getSlot() {
        return $this->slot;
    }
}

?>