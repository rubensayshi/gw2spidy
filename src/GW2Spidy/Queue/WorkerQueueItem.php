<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisQueue\RedisQueueItem;

class WorkerQueueItem extends RedisQueueItem {
    const PRIORITY_EXTREME    = 9999;
    const PRIORITY_HIGH       = 1000;
    const PRIORITY_MED        = 500;
    const PRIORITY_TYPEDB     = 499;
    const PRIORITY_LISTINGSDB = 102;
    const PRIORITY_ITEMDB     = 101;
    const PRIORITY_LOW        = 100;
    const PRIORITY_VERY_LOW   = 0;

    protected $worker;

    public function setWorker($worker) {
        $this->worker = $worker;

        return $this;
    }

    public function getWorker() {
        return $this->worker;
    }

}

?>