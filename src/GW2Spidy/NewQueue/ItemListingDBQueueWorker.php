<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \Exception;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

use GW2Spidy\TradingPostSpider;


class ItemListingDBQueueWorker extends BaseWorker {
    public function work($workload) {
        $items = array();

        if ($workload instanceof ItemListingDBQueueItem) {
            $item = $workload->getItem();
            $items[$item->getDataId()] = $workload->getItem();

            $this->updateListings($item);
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
        if ($itemsData = TradingPostSpider::getInstance()->getItemsByIds(array_keys($items))) {
            $exceptions = array();

            foreach ($itemsData as $itemData) {
                try {
                    $this->updateListingFromItemData($itemData, $items[$itemData['data_id']]);
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

    protected function updateListings(Item $item) {
        $now  = new DateTime();

        $item->setLastUpdated($now);

        $listings = TradingPostSpider::getInstance()->getAllListingsById($item->getDataId());
        $sell = $listings[TradingPostSpider::LISTING_TYPE_SELL];
        $buy  = $listings[TradingPostSpider::LISTING_TYPE_BUY];

        $lowestSell = null;
        $lowestBuy  = null;

        $sellQuantityAmount = 0;
        $sellListingsAmount = 0;
        if (count($sell)) {
            $lowestSell = reset($sell);

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
            $sellListing->setUnitPrice($lowestSell['unit_price']);
            $item->setMinSaleUnitPrice($lowestSell['unit_price']);
        } else {
            $sellListing->setUnitPrice($item->getMinSaleUnitPrice());
        }

        $item->setSaleAvailability($sellQuantityAmount);

        $sellListing->save();

        $buyQuantityAmount = 0;
        $buyListingsAmount = 0;
        if (count($buy)) {
            $lowestBuy = reset($buy);

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

        if ($lowestBuy) {
            $buyListing->setUnitPrice($lowestBuy['unit_price']);
            $item->setMaxOfferUnitPrice($lowestBuy['unit_price']);
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

