<?php

namespace GW2Spidy\NewQueue;

use \Criteria;

use GW2Spidy\Util\Functions;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemDBQueueWorker extends BaseWorker {
    public function work(ItemDBQueueItem $item) {
        $res = $this->buildItemDB($item->getType(), $item->getSubType(), $item->getOffset());

        // we stop enqueueing the next slice when we stop getting results
        if ($item->getFull() && $res) {
            $this->enqeueNextOffset($item);
        }
    }

    public function buildItemDB($type, $subtype, $offset) {
        var_dump((string)$type, (string)$subtype, $offset) . "\n\n";

        $items = TradingPostSpider::getInstance()->getItemList($type, $subtype, $offset);

        if ($items) {
            foreach ($items as $itemData) {
                $this->storeItemData($itemData, $type, $subtype);
            }

            return true;
        }
    }

    public function storeItemData($itemData, ItemType $type = null, ItemSubType $subtype = null, $item = null) {
        $itemData = $this->unifyItemData($itemData);
        // this seems to be removed items o.O?
        if (!isset($itemData['name']) && !isset($itemData['rarity']) && !isset($itemData['restriction_level']) && isset($itemData['data_id'])) {
            return;
        }

        $now  = new \DateTime();
        $item = $item ?: ItemQuery::create()->findPK($itemData['data_id']);

        $updateItemData = $itemData;

        // dont update this data data here, processed elsewhere ;-)
        unset($updateItemData['min_sale_unit_price']);
        unset($updateItemData['sale_availability']);
        unset($updateItemData['max_offer_unit_price']);
        unset($updateItemData['offer_availability']);

        if ($item) {
            $p = Functions::almostEqualCompare($updateItemData['name'], $item->getName());

            if ($p < 50) {
                echo "Title for ID no longer matches! item [json::{$updateItemData['data_id']}::{$updateItemData['name']}] vs [db::{$item->getDataId()}::{$item->getName()}] [{$p}%]";
            }

            // dont update crap items
            if (!$updateItemData['name'] || $updateItemData['name'] == '...' || $updateItemData['name'] == 'Encrypted') {
                $updateItemData = null;
            }
        } else {
            $item = new Item();
        }

        if ($updateItemData) {
            $item->fromArray($updateItemData, \BasePeer::TYPE_FIELDNAME);
            $item->setTpName($updateItemData['name']);

            if (isset($updateItemData['level'])) {
                $item->setRestrictionLevel($updateItemData['level']);
            }

            if ($type) {
                $item->setItemType($type);
            }
            if ($subtype) {
                $item->setItemSubType($subtype);
            }
        }

        if (getAppConfig('gw2spidy.save_listing_from_item_data')) {
            $this->processListingsFromItemData($itemData, $item, false);
        }

        $item->save();

        return $item;
    }


    protected function enqeueNextOffset(ItemDBQueueItem $item) {
        $item = clone $item;
        $item->addOffset(10);

        $this->manager->enqueue($item);
    }
}

