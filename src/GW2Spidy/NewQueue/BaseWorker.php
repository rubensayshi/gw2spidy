<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \DateInterval;
use \Criteria;
use \Exception;

use GW2Spidy\Util\Functions;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\TradingPostSpider;


class BaseWorker {
    const ERROR_CODE_NO_LONGER_EXISTS = 444441;

    protected $manager;

    public function __construct($manager) {
        $this->manager = $manager;
    }

    protected function processListingsFromItemData($itemData, $item = null, $save = true) {
        $now  = new DateTime();
        $item = $item ?: ItemQuery::create()->findPK($itemData['data_id']);

        $item->setOfferAvailability($itemData['sale_availability']);
        if (isset($itemData['min_sale_unit_price']) && $itemData['min_sale_unit_price'] > 0) {
            $item->setMinSaleUnitPrice($itemData['min_sale_unit_price']);

            $sellListing = new SellListing();
            $sellListing->setItem($item);
            $sellListing->setListingDatetime($now);
            $sellListing->setQuantity($itemData['sale_availability'] ?: 0);
            $sellListing->setUnitPrice($itemData['min_sale_unit_price']);
            $sellListing->setListings(1);

            $sellListing->save();
        }

        $item->setOfferAvailability($itemData['offer_availability']);
        if (isset($itemData['max_offer_unit_price']) && $itemData['max_offer_unit_price'] > 0) {
            $item->setMaxOfferUnitPrice($itemData['max_offer_unit_price']);

            $buyListing = new BuyListing();
            $buyListing->setItem($item);
            $buyListing->setListingDatetime($now);
            $buyListing->setQuantity($itemData['offer_availability'] ?: 0);
            $buyListing->setUnitPrice($itemData['max_offer_unit_price']);
            $buyListing->setListings(1);

            $buyListing->save();
        }

        if ($save) {
            $item->save();
        }
    }
}

