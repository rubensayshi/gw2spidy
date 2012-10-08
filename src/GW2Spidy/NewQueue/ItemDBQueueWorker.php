<?php

namespace GW2Spidy\NewQueue;

use \Criteria;

use GW2Spidy\DB\BuyListing;

use GW2Spidy\DB\SellListing;

use GW2Spidy\Util\Functions;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemDBQueueWorker {
    const ERROR_CODE_NO_LONGER_EXISTS = 444441;

    protected $manager;

    public function __construct(ItemDBQueueManager $manager) {
        $this->manager = $manager;
    }

    public function work(ItemDBQueueItem $item) {
        $res = $this->buildItemDB($item->getType(), $item->getSubType(), $item->getOffset());

        // we stop enqueueing the next slice when we stop getting results
        if ($item->getFull() && $res) {
            $this->enqeueNextOffset($item);
        }
    }

    protected function buildItemDB($type, $subtype, $offset) {
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

        // not sure when this happens, I think when the item is no longer on the tradingpost at all
        // it has an ID and img, but nothing more ... skipping it silently for now
        if (!isset($itemData['name']) && !isset($itemData['rarity']) && !isset($itemData['restriction_level']) && isset($itemData['data_id'])) {
            return;
        }

        $now  = new \DateTime();
        $item = $item ?: ItemQuery::create()->findPK($itemData['data_id']);

        // done based on listings, ignore this data here ;-)
        unset($itemData['min_sale_unit_price']);
        unset($itemData['sale_availability']);
        unset($itemData['max_offer_unit_price']);
        unset($itemData['offer_availability']);

        var_dump($itemData['name']) . "\n\n";
        if ($item) {
            if (($p = Functions::almostEqualCompare($itemData['name'], $item->getName())) > 50 || $item->getName() == "...") {
                $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);

                if ($type) {
                    $item->setItemType($type);
                }
                if ($subtype) {
                    $item->setItemSubType($subtype);
                }

                $item->save();
            } else {
                throw new \Exception("Title for ID no longer matches! item [{$p}] [json::{$itemData['data_id']}::{$itemData['name']}] vs [db::{$item->getDataId()}::{$item->getName()}]", self::ERROR_CODE_NO_LONGER_EXISTS);
            }
        } else {
            $item = new Item();
            $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);

            if ($type) {
                $item->setItemType($type);
            }
            if ($subtype) {
                $item->setItemSubType($subtype);
            }

            $item->save();
        }

        return $item;
    }


    protected function enqeueNextOffset(ItemDBQueueItem $item) {
        $item = clone $item;
        $item->addOffset(10);

        $this->manager->enqueue($item);
    }
}

