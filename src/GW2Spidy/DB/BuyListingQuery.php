<?php

namespace GW2Spidy\DB;

use \DateTime;
use \DateTimeZone;
use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseBuyListingQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'buy_listing' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class BuyListingQuery extends BaseBuyListingQuery {
    public static function getChartDatasetDataForItem(Item $item) {
        $cacheKey = __CLASS__ . "::" . __METHOD__ . "::" . $item->getDataId();
        $data     = ApplicationCache::getInstance()->get($cacheKey);

        if (!$data) {
            $data = array(
            	'raw' => array(),
            	'daily' => array(),
            	'weekly' => array(),
            	'monthly' => array(),
            );

            $listings = static::create()
                            ->select(array('listingDate', 'listingTime'))
                            ->withColumn('MIN(unit_price)', 'min_unit_price')
                            ->groupBy('listingDate')
                            ->groupBy('listingTime')
                            ->filterByItemId($item->getDataId())
                            ->find();

            $dailyValues = array();
            $weeklyValues = array();
            $monthlyValues = array();
            foreach ($listings as $listingEntry) {
                $date = new DateTime("{$listingEntry['listingDate']} {$listingEntry['listingTime']}");
                $date->setTimezone(new DateTimeZone('UTC'));
                $timestamp = $date->getTimestamp();

                $dailyValues[$timestamp] = $listingEntry['min_unit_price'];
                $weeklyValues[$timestamp] = $listingEntry['min_unit_price'];
                $monthlyValues[$timestamp] = $listingEntry['min_unit_price'];
                $listingEntry['min_unit_price'] = round($listingEntry['min_unit_price'], 2);

                $data['raw'][] = array($timestamp*1000, $listingEntry['min_unit_price']);
                $data['daily'][] = array($timestamp*1000, round(array_sum($dailyValues)/count($dailyValues), 2));
                $data['weekly'][] = array($timestamp*1000, round(array_sum($weeklyValues)/count($weeklyValues), 2));
                $data['monthly'][] = array($timestamp*1000, round(array_sum($monthlyValues)/count($monthlyValues), 2));
                
                /**
                 * @TODO: This might be optimizable, don't feel like thinking about it though
                 */
                foreach ($dailyValues as $keyTimestamp => $value) {
                	if ($timestamp - $keyTimestamp > 86400/*24*3600*/) {
                		unset($dailyValues[$keyTimestamp]);
                	}
                }
                foreach ($weeklyValues as $keyTimestamp => $value) {
                	if ($timestamp - $keyTimestamp > 604800/*24*3600*7*/) {
                		unset($weeklyValues[$keyTimestamp]);
                	}
                }
                foreach ($monthlyValues as $keyTimestamp => $value) {
                	if ($timestamp - $keyTimestamp > 18144000/*24*3600*7*30*/) {
                		unset($monthlyValues[$keyTimestamp]);
                	}
                }
            }

            ApplicationCache::getInstance()->set($cacheKey, $data, MEMCACHE_COMPRESSED, 600);
        }

        return $data;
    }

} // BuyListingQuery
