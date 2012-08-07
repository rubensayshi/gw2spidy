<?php

namespace GW2Spidy\Util\RedisQueue;

class RedisPriorityQueueItem extends RedisQueueItem {
    protected $priority;

    public function setPriority($priority) {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority() {
        return $this->priority;
    }
}

?>