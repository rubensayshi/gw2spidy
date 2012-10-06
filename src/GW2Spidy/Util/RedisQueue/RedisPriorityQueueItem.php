<?php

namespace GW2Spidy\Util\RedisQueue;

abstract class RedisPriorityQueueItem extends RedisQueueItem {
    abstract public function getPriority();
}

?>