<?php

namespace GW2Spidy\Util\RedisQueue;

abstract class RedisQueueItem {
    protected $manager;

    public function setMananger(RedisQueueManager $manager) {
        $this->manager = $manager;
    }

    abstract public function work();
}

?>