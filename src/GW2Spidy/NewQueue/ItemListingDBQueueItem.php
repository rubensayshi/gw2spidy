<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \Exception;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

use GW2Spidy\Util\RedisQueue\RedisPriorityIdentifierQueueItem;

class ItemListingDBQueueItem {
    protected $item;

    public function __construct($input) {
        if ($input instanceof Item) {
            $this->item = $input;
        } else {
            $this->item = ItemQuery::create()->findPk($input);
        }
    }

    public function getIdentifier() {
        return $this->item->getDataId();
    }

    public function getItem() {
        return $this->item;
    }

    public function getPriority() {
        return $this->item->getQueuePriority();
    }
}

?>