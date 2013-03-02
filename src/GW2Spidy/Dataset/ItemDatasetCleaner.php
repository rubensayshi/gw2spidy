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
    const TS_ONE_MONTH = 2592000;

    const CLEANUP_WEEK = 'week';
    const CLEANUP_MONTH = 'month';

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

    public function clean($cleanup = self::CLEANUP_WEEK) {
        $thres = self::tsHour(time() - ($cleanup == self::CLEANUP_WEEK ? self::TS_ONE_WEEK : self::TS_ONE_MONTH));

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
        $tsGrpd = array();
        foreach ($listings as $listing) {
            $id    = $listing['id'];
            $date  = new DateTime("{$listing['listingDatetime']}");
            $ts    = $date->getTimestamp();
            $tsGrp = $cleanup == self::CLEANUP_WEEK ? self::tsHour($ts) : self::tsDay($ts);

            $ticks[$id] = $listing;
            $tsGrpd[$tsGrp][] = $id;
        }

        foreach ($tsGrpd as $tsGrp => $ids) {
            if ($tsGrp >= $thres) {
                break;
            }

            if ($count > 100) {
                break;
            }

            if (count($ids) <= 1) {
                continue;
            }

            $grpRates = array();
            $grpQuantities = array();
            $grpListings = array();

            foreach ($ids as $id) {
                $grpRates[] = $ticks[$id]['unitPrice'];
                $grpQuantities[] = $ticks[$id]['quantity'];
                $grpListings[] = $ticks[$id]['listings'];
            }

            $grpRAvg = array_sum($grpRates) / count($grpRates);
            $grpQAvg = array_sum($grpQuantities) / count($grpQuantities);
            $grpLAvg = array_sum($grpListings) / count($grpListings);

            $con->beginTransaction();

            $new = $this->type == self::TYPE_SELL_LISTING ? new SellListing() : new BuyListing();
            $new->setListingDatetime($tsGrp);
            $new->setItemId($this->itemId);
            $new->setUnitPrice($grpRAvg);
            $new->setQuantity($grpQAvg);
            $new->setListings($grpLAvg);
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
