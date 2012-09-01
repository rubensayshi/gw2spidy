<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\Queue\WorkerQueueItem;

interface Worker {
    public function getRetries();

    public function work(WorkerQueueItem $item);
}

?>