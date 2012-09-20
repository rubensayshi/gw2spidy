<?php

namespace GW2Spidy\DB;

use GW2Spidy\ListingQueryHelper;

use \DateTime;
use \DateTimeZone;
use GW2Spidy\Util\ApplicationCache;
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
        return ListingQueryHelper::getChartDatasetDataForItem($item, self::create());
    }

} // SellListingQuery
