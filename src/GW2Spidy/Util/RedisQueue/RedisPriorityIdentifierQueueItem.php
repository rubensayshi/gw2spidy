<?php

namespace GW2Spidy\Util\RedisQueue;

abstract class RedisPriorityIdentifierQueueItem extends RedisPriorityQueueItem {
    protected $identifier;

    public function __construct($identifier) {
        $this->identifier = $identifier;
    }

    abstract public function getIdentifier();
}

?>