<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \DateInterval;
use \Criteria;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

use GW2Spidy\Util\Functions;
use GW2Spidy\TradingPostSpider;


class ItemListingDBQueueWorker {
    protected $manager;

    public function __construct(ItemListingDBQueueManager $manager) {
        $this->manager = $manager;
    }

    public function work(ItemListingDBQueueItem $queueItem) {
        $this->updateListings($queueItem);
        $this->updateTrending($queueItem);
    }

    protected function updateListings(ItemListingDBQueueItem $queueItem) {
        $item = $queueItem->getItem();
        $now  = new DateTime();

        $listings = TradingPostSpider::getInstance()->getAllListingsById($item->getDataId());
        $sell = $listings[TradingPostSpider::LISTING_TYPE_SELL];
        $buy  = $listings[TradingPostSpider::LISTING_TYPE_BUY];

        $lowestSell = null;
        $lowestBuy  = null;

        $q = 0;
        $l = 0;
        if (count($sell)) {
            $lowestSell = reset($sell);

            foreach ($sell as $s) {
                $q += $s['quantity'];
                $l += $s['listings'];
            }
        }

        $sellListing = new SellListing();
        $sellListing->setItem($item);
        $sellListing->setListingDatetime($now);
        $sellListing->setQuantity($q);
        $sellListing->setListings($l);

        if ($lowestSell) {
            $sellListing->setUnitPrice($lowestSell['unit_price']);
            $item->setMinSaleUnitPrice($lowestSell['unit_price']);
        }

        $sellListing->save();

        $q = 0;
        $l = 0;
        if (count($buy)) {
            $lowestBuy = reset($buy);

            foreach ($buy as $b) {
                $q += $b['quantity'];
                $l += $b['listings'];
            }
        }

        $buyListing = new BuyListing();
        $buyListing->setItem($item);
        $buyListing->setListingDatetime($now);
        $buyListing->setQuantity($q);
        $sellListing->setListings($l);

        if ($lowestBuy) {
            $buyListing->setUnitPrice($lowestBuy['unit_price']);
            $item->setMaxOfferUnitPrice($lowestBuy['unit_price']);
        }

        $buyListing->save();

        $item->save();
    }

    protected function updateTrending(ItemListingDBQueueItem $queueItem) {
        $item = $queueItem->getItem();

        if ($queueItem->getItemPriority() > ItemListingDBQueueItem::ONE_HOUR) {
            $item->setSalePriceChangeLastHour(0);
            $item->setOfferPriceChangeLastHour(0);

            $item->save();

            return;
        }

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

