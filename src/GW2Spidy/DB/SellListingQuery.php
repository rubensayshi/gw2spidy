<?php

namespace GW2Spidy\DB;

use \DateTime;
use GW2Spidy\DB\om\BaseSellListingQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'sell_listing' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class SellListingQuery extends BaseSellListingQuery {
    public static function getChartDatasetDataForItem(Item $item) {
        $data = array();

        $listings = static::create()
                        ->select(array('listingDate', 'listingTime'))
                        ->withColumn('MIN(unit_price)', 'min_unit_price')
                        ->groupBy('listingDate')
                        ->groupBy('listingTime')
                        ->filterByItemId($item->getDataId())
                        ->find();

        foreach ($listings as $listingEntry) {
            $date = new DateTime("{$listingEntry['listingDate']} {$listingEntry['listingTime']} UTC");

            $listingEntry['min_unit_price'] = round($listingEntry['min_unit_price'], 2);

            $data[] = array($date->getTimestamp()*1000, $listingEntry['min_unit_price']);
        }

        return $data;
    }

} // SellListingQuery
