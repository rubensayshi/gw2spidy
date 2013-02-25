<?php

namespace GW2Spidy\Dataset;

use GW2Spidy\Application;

use GW2Spidy\DB\Item;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

class ItemDataset extends BaseDataset {
    /*
     * different posible type of datasets we can have
     */
    const TYPE_SELL_LISTING = 'sell_listing';
    const TYPE_BUY_LISTING  = 'buy_listing';

    /**
     * @var  int    $itemId
     */
    protected $itemId;

    /**
     * one of the self::TYPE_ constants
     * @var $type
     */
    protected $type;

    /**
     * @param  int       $itemId
     * @param  string    $type        should be one of self::TYPE_
     */
    public function __construct($itemId, $type) {
        $this->itemId = $itemId;
        $this->type = $type;
    }

    /**
     * update the current dataset with new values from the database
     *  if posible only with values since our lastUpdated moment
     */
    public function updateDataset() {
        if ($this->updated) {
            return;
        }

        $limit = 20000;
        $end   = null;
        $start = $this->lastUpdated;
        $con = \Propel::getConnection();

        $table = $this->type == self::TYPE_SELL_LISTING ? 'sell_listing' : 'buy_listing';

        $and = "";
        // only retrieve new ticks since last update
        if ($start) {
            $and = " AND listing_datetime > '{$start->format('Y-m-d H:i:s')}'";
        }

        $stmt = $con->prepare("
                SELECT
                listing_datetime AS listingDatetime,
                MIN(unit_price) AS min_unit_price
                FROM {$table}
                WHERE item_id = {$this->itemId}
                {$and}
                GROUP BY listing_datetime
                ORDER BY listing_datetime ASC
                LIMIT {$limit}");

        $stmt->execute();
        $listings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($listings as $listing) {
            $date = new DateTime("{$listing['listingDatetime']}");
            $rate = intval($listing['min_unit_price']);

            $end = $date;

            $this->processTick($date, $rate);
        }

        if (!($this->uptodate = count($listings) != $limit)) {
            $app = Application::getInstance();
            $app['no_cache'] = true;
        }

        // update for next time
        $this->updated = true;
        if ($end) {
            $this->lastUpdated = $end;
        }
    }
}
