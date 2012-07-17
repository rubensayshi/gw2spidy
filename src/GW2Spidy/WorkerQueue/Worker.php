<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\DB\WorkerQueueItem;

interface Worker {
    public function work(WorkerQueueItem $item);
}

?>