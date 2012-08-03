<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\Queue\WorkerQueueItem;

interface Worker {
    public function work(WorkerQueueItem $item);
}

?>