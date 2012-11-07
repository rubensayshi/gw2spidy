<?php

namespace GW2Spidy\Dataset;

use \DateTime;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

class ItemDatasetCleaner {
    /*
     * different posible type of datasets we can have
     */
    const TYPE_SELL_LISTING = 'sell_listing';
    const TYPE_BUY_LISTING  = 'buy_listing';

    /*
     * just easy constants to make the code more readable
     */
    const TS_ONE_HOUR = 3600;
    const TS_ONE_DAY  = 86400;
    const TS_ONE_WEEK = 604800;

    /**
     * @var  int    $itemId
     */
    protected $itemId;

    /**
     * one of the self::TYPE_ constants
     * @var $type
     */
    protected $type;

    /*
     * helper methods to round timestamps by hour / day / week
     */
    public static function tsHour($ts) {
        return ceil($ts / self::TS_ONE_HOUR) * self::TS_ONE_HOUR;
    }
    public static function tsDay($ts) {
        return ceil($ts / self::TS_ONE_DAY) * self::TS_ONE_DAY;
    }
    public static function tsWeek($ts) {
        return ceil($ts / self::TS_ONE_WEEK) * self::TS_ONE_WEEK;
    }

    /**
     * @param  int       $itemId
     * @param  string    $type        should be one of self::TYPE_
     */
    public function __construct($itemId, $type) {
        $this->itemId = $itemId;
        $this->type = $type;
    }

    public function clean() {
        $count = 0;
        $con = \Propel::getConnection();

        $table = $this->type == self::TYPE_SELL_LISTING ? 'sell_listing' : 'buy_listing';
        $stmt = $con->prepare("
                SELECT
                    id, listing_datetime AS listingDatetime, unit_price AS unitPrice, listings, quantity
                FROM {$table}
                WHERE item_id = {$this->itemId}
                ORDER BY listing_datetime ASC");

        $stmt->execute();
        $listings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ticks = array();
        $tsByHour = array();
        foreach ($listings as $listing) {
            $id   = $listing['id'];
            $date = new DateTime("{$listing['listingDatetime']}");
            $ts   = $date->getTimestamp();
            $tsHr = self::tsHour($ts);

            $ticks[$id] = $listing;
            $tsByHour[$tsHr][] = $id;
        }

        $thres = self::tsHour(time() - self::TS_ONE_WEEK);
        foreach ($tsByHour as $tsHr => $ids) {
            if ($tsHr >= $thres) {
                break;
            }

            if ($count > 100) {
                break;
            }

            if (count($ids) <= 1) {
                continue;
            }

            $hourRates = array();
            $hourQuantities = array();
            $hourListings = array();

            foreach ($ids as $id) {
                $hourRates[] = $ticks[$id]['unitPrice'];
                $hourQuantities[] = $ticks[$id]['quantity'];
                $hourListings[] = $ticks[$id]['listings'];
            }

            $hourRAvg = array_sum($hourRates) / count($hourRates);
            $hourQAvg = array_sum($hourQuantities) / count($hourQuantities);
            $hourLAvg = array_sum($hourListings) / count($hourListings);

            $con->beginTransaction();

            $new = $this->type == self::TYPE_SELL_LISTING ? new SellListing() : new BuyListing();
            $new->setListingDatetime($tsHr);
            $new->setItemId($this->itemId);
            $new->setUnitPrice($hourRAvg);
            $new->setQuantity($hourQAvg);
            $new->setListings($hourLAvg);
            $new->save();

            $q = $this->type == self::TYPE_SELL_LISTING ? new SellListingQuery() : new BuyListingQuery();
            $q->filterById($ids, \Criteria::IN)
              ->delete();

            $con->commit();

            $count++;
        }

        unset($ticks, $tsByHour);

        return $count;
    }
}
