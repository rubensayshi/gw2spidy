<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \Exception;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

use GW2Spidy\OfficialTPAPISpider;
use GW2Spidy\TradingPostSpider;


class ItemListingDBQueueWorker extends BaseWorker {
    public function work($workload) {
        $items = array();

        if ($workload instanceof ItemListingDBQueueItem) {
            throw new \Exception("No longer supported!");
        } else {
            foreach ($workload as $queueItem) {
                $item = $queueItem->getItem();
                $items[$item->getDataId()] = $queueItem->getItem();
            }

            $this->massUpdateListings($items);
        }

        foreach ($items as $item) {
            $this->updateTrending($item);
        }
    }

    public function massUpdateListings($items) {
        $listings = OfficialTPAPISpider::getInstance()->getListingsByIds(array_keys($items));

        if ($listings) {
            $exceptions = array();

            foreach ($listings as $itemListings) {
                try {
                    $this->updateListings($itemListings);
                } catch (Exception $e) {
                    if (strstr("CurlRequest failed [[ 401 ]]", $e->getMessage())) {
                        continue;
                    }

                    $exceptions[] = $e;
                }
            }

            if (count($exceptions) == 1) {
                throw $exceptions[0];
            } else if (count($exceptions) > 1) {
                $s = "";
                foreach ($exceptions as $e) {
                    $s .= $e->getMessage() . " \n ------------ \n";
                }

                throw new Exception("Multiple Exceptions were thrown: \n {$s}");
            }
        }
    }
    public function updateListingFromItemData($itemData, $item = null) {
        // this seems to be removed items o.O?
        if (!isset($itemData['name']) && !isset($itemData['rarity']) && !isset($itemData['restriction_level']) && isset($itemData['data_id'])) {
            return;
        }

        $this->processListingsFromItemData($itemData, $item);
    }

    protected function updateListings($listings) {
        $now  = new DateTime();

        $item = ItemQuery::create()->findOneByDataId($listings['id']);

        $item->setLastUpdated($now);

        $sell = $listings['sells'];
        $buy  = $listings['buys'];

        $lowestSell = null;
        $maxBuy     = null;

        $sellQuantityAmount = 0;
        $sellListingsAmount = 0;
        if (count($sell)) {
            $lowestSell = min(array_map(function($row) { return $row['unit_price']; }, $sell));

            foreach ($sell as $s) {
                $sellQuantityAmount += $s['quantity'];
                $sellListingsAmount += $s['listings'];
            }
        }

        $sellListing = new SellListing();
        $sellListing->setItem($item);
        $sellListing->setListingDatetime($now);
        $sellListing->setQuantity($sellQuantityAmount);
        $sellListing->setListings($sellListingsAmount);

        if ($lowestSell) {
            $sellListing->setUnitPrice($lowestSell);
            $item->setMinSaleUnitPrice($lowestSell);
        } else {
            $sellListing->setUnitPrice($item->getMinSaleUnitPrice());
        }

        $item->setSaleAvailability($sellQuantityAmount);

        $sellListing->save();

        $buyQuantityAmount = 0;
        $buyListingsAmount = 0;
        if (count($buy)) {
            $maxBuy = max(array_map(function($row) { return $row['unit_price']; }, $buy));

            foreach ($buy as $b) {
                $buyQuantityAmount += $b['quantity'];
                $buyListingsAmount += $b['listings'];
            }
        }

        $buyListing = new BuyListing();
        $buyListing->setItem($item);
        $buyListing->setListingDatetime($now);
        $buyListing->setQuantity($buyQuantityAmount);
        $buyListing->setListings($buyListingsAmount);

        if ($maxBuy) {
            $buyListing->setUnitPrice($maxBuy);
            $item->setMaxOfferUnitPrice($maxBuy);
        } else {
            $buyListing->setUnitPrice($item->getMaxOfferUnitPrice());
        }

        $item->setOfferAvailability($buyQuantityAmount);

        $buyListing->save();

        $item->save();
    }

    protected function updateTrending(Item $item) {
        $onehourago = new DateTime();
        $onehourago->sub(new \DateInterval('PT1H'));

        $q = SellListingQuery::create()
                ->filterByItemId($item->getDataId())
                ->filterByListingDatetime($onehourago, \Criteria::GREATER_THAN)
                ->orderByListingDatetime(\Criteria::ASC);

        $oneHourAgoSellListing = $q->findOne();

        if (!$oneHourAgoSellListing || $oneHourAgoSellListing->getUnitPrice() <= 0 || $item->getMinSaleUnitPrice() <= 0) {
            $item->setSalePriceChangeLastHour(0);
        } else {
            $item->setSalePriceChangeLastHour((($item->getMinSaleUnitPrice() - $oneHourAgoSellListing->getUnitPrice()) / $oneHourAgoSellListing->getUnitPrice()) * 100);
        }

        $q = BuyListingQuery::create()
                ->filterByItemId($item->getDataId())
                ->filterByListingDatetime($onehourago, \Criteria::GREATER_THAN)
                ->orderByListingDatetime(\Criteria::ASC);

        $oneHourAgoBuyListing = $q->findOne();

        if (!$oneHourAgoBuyListing || $oneHourAgoBuyListing->getUnitPrice() <= 0 || $item->getMaxOfferUnitPrice() <= 0) {
            $item->setOfferPriceChangeLastHour(0);
        } else {
            $item->setOfferPriceChangeLastHour((($item->getMaxOfferUnitPrice() - $oneHourAgoBuyListing->getUnitPrice()) / $oneHourAgoBuyListing->getUnitPrice()) * 100);
        }

        $item->save();
    }
}

