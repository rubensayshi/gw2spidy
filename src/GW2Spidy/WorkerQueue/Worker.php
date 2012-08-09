<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\Queue\WorkerQueueItem;

interface Worker {
    public function work(WorkerQueueItem $item);

    protected function almostEqualCompare($left, $right) {
        return $this->cleanUpStringForCompare($left) == $this->cleanUpStringForCompare($right);
    }

    protected function cleanUpStringForCompare($string) {
        $string = str_replace(" ", "", $string);
        $string = str_replace("'s", "", $string);
        $string = strtolower($string);

        return $string;
    }
}

?>