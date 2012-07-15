<?php

namespace GW2Spidy\Spider;


use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\TradeMarket\TradeMarket;

class ItemDBSpider {
    public function run() {
        $this->buildItemTypeDB();
        $this->buildItemDB();

    }

    public function buildItemDB() {
        $market = TradeMarket::getInstance();

        foreach (ItemTypeQuery::create()->find() as $type) {
            foreach ($type->getSubTypes() as $subtype) {
                $offset = 0;

                while (($items = $market->getItemList($type, $subtype, $offset))) {
                    var_dump($subtype->getTitle(), $offset, count($items));
                    foreach ($items as $itemData) {
                        $item = ItemQuery::create()->findPK($itemData['data_id']);

                        if ($item) {
                            if ($item->getName() != $itemData['name']) {
                                throw new \Exception("Title for ID no longer matches! [{$itemData['name']}] [{$itemData['id']}]");
                            }
                        } else {
                            $item = new Item();
                            $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);
                            $item->setItemType($type);
                            $item->setItemSubType($subtype);

                            $item->save();
                        }
                    }

                    $offset += 10;
                }

            }
        }

    }

    public function buildItemTypeDB() {
        $market = TradeMarket::getInstance();

        $marketData = $market->getMarketData();

        foreach ((array)$marketData['types'] as $mainTypeData) {
            if (!isset($mainTypeData['id']) || !isset($mainTypeData['name'])) {
                continue;
            }

            $type = ItemTypeQuery::create()->findPK($mainTypeData['id']);

            if ($type) {
                if ($type->getTitle() != $mainTypeData['name']) {
                    throw new \Exception("Title for ID no longer matches! [{$mainTypeData['name']}] [{$mainTypeData['id']}]");
                }
            } else {
                $type = new ItemType();
                $type->setId($mainTypeData['id']);
                $type->setTitle($mainTypeData['name']);
                $type->save();
            }

            foreach ((array)$mainTypeData['subs'] as $subTypeData) {
                if (!isset($subTypeData['id']) || !isset($subTypeData['name'])) {
                    continue;
                }

                $subtype = ItemSubTypeQuery::create()->findPK(array($subTypeData['id'], $type->getId()));

                if ($subtype) {
                    if ($subtype->getTitle() != $subTypeData['name']) {
                        throw new \Exception("Title for ID no longer matches! [{$subTypeData['name']}] [{$subTypeData['id']}]");
                    }
                    if (!$subtype->getMainType()->equals($type)) {
                        throw new \Exception("Maintype no longer matches! [{$subTypeData['name']}] [{$subTypeData['id']}]");
                    }
                } else {
                    $subtype = new ItemSubType();
                    $subtype->setMainType($type);
                    $subtype->setId($subTypeData['id']);
                    $subtype->setTitle($subTypeData['name']);
                    $subtype->save();
                }
            }
        }
    }
}

?>