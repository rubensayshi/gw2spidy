<?php

namespace GW2Spidy\DB;

use GW2Spidy\ListingQueryHelper;

use \DateTime;
use \DateTimeZone;

use GW2Spidy\Util\ApplicationCache;

use GW2Spidy\DB\om\BaseGoldToGemRateQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'gold_to_gem_rate' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class GoldToGemRateQuery extends BaseGoldToGemRateQuery {
    public static function getChartDatasetData() {
        return ListingQueryHelper::getChartDatasetData(self::create());
    }
} // GoldToGemRateQuery
