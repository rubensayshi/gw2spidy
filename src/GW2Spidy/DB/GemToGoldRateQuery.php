<?php

namespace GW2Spidy\DB;

use \DateTime;
use \DateTimeZone;

use GW2Spidy\Util\ApplicationCache;

use GW2Spidy\DB\om\BaseGemToGoldRateQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'gem_to_gold_rate' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class GemToGoldRateQuery extends BaseGemToGoldRateQuery {
    public static function getChartDatasetData() {
        $cacheKey = __CLASS__ . "::" . __METHOD__;
        $data     = ApplicationCache::getInstance()->get($cacheKey);

        if (!$data) {
            $data = array();

            $rates = static::create()
                            ->select(array('rateDatetime', 'rate'))
                            ->find();

            foreach ($rates as $rateEntry) {
                $date = new DateTime("{$rateEntry['rateDatetime']}");
                $date->setTimezone(new DateTimeZone('UTC'));

                $rateEntry['rate'] = round($rateEntry['rate'], 2);

                $data[] = array($date->getTimestamp()*1000, $rateEntry['rate']);
            }

            ApplicationCache::getInstance()->set($cacheKey, $data, MEMCACHE_COMPRESSED, 600);
        }

        return $data;
    }
} // GemToGoldRateQuery
