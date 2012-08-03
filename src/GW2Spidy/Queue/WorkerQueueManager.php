<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisQueue\RedisQueueManager;

class WorkerQueueManager extends RedisQueueManager {
    protected function getQueueName() {
        return "worker-queue";
    }
}

?>