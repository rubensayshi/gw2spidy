<?php

namespace GW2Spidy;


use \DateTime;
use \DateTimeZone;
use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\Item;

class ListingQueryHelper {
    public static function getChartDatasetDataForItem(Item $item, \ModelCriteria $q) {
        $cacheKey = get_class($q) . "::" . __METHOD__ . "::" . $item->getDataId();
        $data     = ApplicationCache::getInstance()->get($cacheKey);

        if (!$data) {
            $data = array(
            	'raw'     => array(),
            	'daily'   => array(),
            	'weekly'  => array(),
            	'monthly' => array(),
            );

            $listings = $q->select(array('listingDate', 'listingTime'))
                          ->withColumn('MIN(unit_price)', 'min_unit_price')
                          ->groupBy('listingDate')
                          ->groupBy('listingTime')
                          ->filterByItemId($item->getDataId())
                          ->find();

            /*
             * use these 3 arrays to maintain the values over which we calculate the moving average
             *  every value is added to the array, but we pop off values older then the specified threshold (day, week, month)
             */
            $dailyValues   = array();
            $weeklyValues  = array();
            $monthlyValues = array();
            foreach ($listings as $listingEntry) {

                $date = new DateTime("{$listingEntry['listingDate']} {$listingEntry['listingTime']}");
                $date->setTimezone(new DateTimeZone('UTC'));
                $timestamp = $date->getTimestamp();

                $dailyValues[$timestamp]   = $listingEntry['min_unit_price'];
                $weeklyValues[$timestamp]  = $listingEntry['min_unit_price'];
                $monthlyValues[$timestamp] = $listingEntry['min_unit_price'];
                $listingEntry['min_unit_price'] = round($listingEntry['min_unit_price'], 2);

                foreach ($dailyValues as $keyTimestamp => $value) {
                    if ($timestamp - $keyTimestamp > 86400/* 1 day */) {
                        unset($dailyValues[$keyTimestamp]);
                    } else {
                        break;
                    }
                }
                foreach ($weeklyValues as $keyTimestamp => $value) {
                    if ($timestamp - $keyTimestamp > 604800/* 7 days */) {
                        unset($weeklyValues[$keyTimestamp]);
                    } else {
                        break;
                    }
                }
                foreach ($monthlyValues as $keyTimestamp => $value) {
                    if ($timestamp - $keyTimestamp > 18144000/* 30 days */) {
                        unset($monthlyValues[$keyTimestamp]);
                    } else {
                        break;
                    }
                }

                $data['raw'][]     = array($timestamp*1000, $listingEntry['min_unit_price']);
                $data['daily'][]   = array($timestamp*1000, round(array_sum($dailyValues)   / count($dailyValues),   2));
                $data['weekly'][]  = array($timestamp*1000, round(array_sum($weeklyValues)  / count($weeklyValues),  2));
                $data['monthly'][] = array($timestamp*1000, round(array_sum($monthlyValues) / count($monthlyValues), 2));
            }

            ApplicationCache::getInstance()->set($cacheKey, $data, MEMCACHE_COMPRESSED, 600);
        }

        return $data;
    }

}
