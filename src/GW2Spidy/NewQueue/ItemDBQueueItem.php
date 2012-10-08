<?php

namespace GW2Spidy\NewQueue;

class ItemDBQueueItem {
    protected $data  = null;

    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    public function getData() {
        return $this->data;
    }
}
