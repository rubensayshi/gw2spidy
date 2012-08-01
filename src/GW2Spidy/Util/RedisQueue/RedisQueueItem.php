<?php

namespace GW2Spidy\Util\RedisQueue;

class RedisQueueItem {
    protected $id    = null;
    protected $queue = '';
    protected $data  = null;


    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setQueue($queue) {
        $this->queue = $queue;

        return $this;
    }

    public function getQueue() {
        return $this->queue;
    }

    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    public function getData() {
        return $this->data;
    }
}

?>