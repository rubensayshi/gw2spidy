<?php

namespace GW2Spidy\DB;

use GW2Spidy\DB\om\BaseWorkerQueueItem;


/**
 * Skeleton subclass for representing a row from the 'worker_queue_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class WorkerQueueItem extends BaseWorkerQueueItem {
    const PRIORITY_EXTREME    = 9999;
    const PRIORITY_HIGH       = 1000;
    const PRIORITY_MED        = 500;
    const PRIORITY_TYPEDB     = 499;
    const PRIORITY_LISTINGSDB = 102;
    const PRIORITY_ITEMDB     = 101;
    const PRIORITY_LOW        = 100;
    const PRIORITY_VERY_LOW   = 0;

    protected $data;

    public function setData($data) {
        $this->data = $data;
        return $this->setRawData(serialize($this->data));
    }

    public function getData() {
        $this->data = unserialize($this->getRawData());

        if ($this->getRawData() && !$this->data) {
            echo $this->getRawData();
            throw new \Exception("Failed to unserialize!");
        }

        return $this->data;
    }

} // WorkerQueueItem
