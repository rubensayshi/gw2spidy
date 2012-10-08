<?php

namespace GW2Spidy\NewQueue;

class ItemDBQueueItem {
    protected $type;
    protected $subtype;
    protected $offset;
    protected $full;

    public function __construct($type = null, $subtype = null, $offset = 0, $full = true) {
        $this->type    = $type;
        $this->subtype = $subtype;
        $this->offset  = $offset;
        $this->full    = $full;
    }

    public function getType() {
        return $this->type;
    }

    public function getSubType() {
        return $this->subtype;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getFull() {
        return $this->full;
    }

    public function addOffset($add) {
        $this->offset += $add;
    }
}
