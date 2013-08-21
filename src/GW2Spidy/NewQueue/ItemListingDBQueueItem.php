<?php

namespace GW2Spidy\NewQueue;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;

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