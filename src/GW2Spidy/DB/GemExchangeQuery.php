<?php

namespace GW2Spidy\DB;

use \DateTime;
use \DateTimeZone;

use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseGemExchangeQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'gem_exchange' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class GemExchangeQuery extends BaseGemExchangeQuery {
    public static function getChartDatasetData() {
        $cacheKey = __CLASS__ . "::" . __METHOD__;
        $data     = ApplicationCache::getInstance()->get($cacheKey);

        if (true || !$data) {
            $data = array();

            $rates = static::create()
                            ->select(array('exchangeDate', 'exchangeTime', 'average'))
                            ->find();

            foreach ($rates as $rateEntry) {
                $date = new DateTime("{$rateEntry['exchangeDate']} {$rateEntry['exchangeTime']}");
                $date->setTimezone(new DateTimeZone('UTC'));

                $rateEntry['average'] = round($rateEntry['average'], 2);

                $data[] = array($date->getTimestamp()*1000, $rateEntry['average']);
            }

            ApplicationCache::getInstance()->set($cacheKey, $data, MEMCACHE_COMPRESSED, 600);
        }

        return $data;
    }

} // GemExchangeQuery
