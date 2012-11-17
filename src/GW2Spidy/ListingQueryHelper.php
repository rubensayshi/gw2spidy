<?php

namespace GW2Spidy;


use GW2Spidy\Util\CacheHandler;

use \DateTime;
use \DateTimeZone;
use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\Item;

class ListingQueryHelper {
	protected static $cache = null;

	protected static function getGemCache() {
		if (is_null(self::$cache)) {
			self::$cache = CacheHandler::getInstance('gem_chart');
		}

		return self::$cache;
	}

    public static function getChartDatasetDataForItem(Item $item, \ModelCriteria $q) {
        $data = array(
        	'raw'     => array(),
        	'daily'   => array(),
        	'weekly'  => array(),
        	'monthly' => array(),
        );

        $listings = $q->select(array('listingDatetime'))
                      ->withColumn('SUM(quantity)', 'quantity')
                      ->withColumn('MIN(unit_price)', 'min_unit_price')
                      ->groupBy('listingDatetime')
                      ->filterByItemId($item->getDataId())
                      ->find();

        /*
         * use these 3 arrays to maintain the values over which we calculate the moving average
         *  every value is added to the array, but we pop off values older then the specified threshold (day, week, month)
         */
        $dailyCntValues = array();
        $dailyValues    = array();
        $weeklyValues   = array();
        $monthlyValues  = array();
        foreach ($listings as $listingEntry) {

            $date = new DateTime("{$listingEntry['listingDatetime']}");
            $date->setTimezone(new DateTimeZone('UTC'));
            $timestamp = $date->getTimestamp();

            $dailyCntValues[$timestamp] = $listingEntry['quantity'];
            $dailyValues[$timestamp]    = $listingEntry['min_unit_price'];
            $weeklyValues[$timestamp]   = $listingEntry['min_unit_price'];
            $monthlyValues[$timestamp]  = $listingEntry['min_unit_price'];
            $listingEntry['min_unit_price'] = round($listingEntry['min_unit_price'], 2);

            foreach ($dailyCntValues as $keyTimestamp => $value) {
                if ($timestamp - $keyTimestamp > 86400/* 1 day */) {
                    unset($dailyCntValues[$keyTimestamp]);
                } else {
                    break;
                }
            }
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

            $data['raw'][]      = array($timestamp*1000, $listingEntry['min_unit_price']);
            $data['daily'][]    = array($timestamp*1000, round(array_sum($dailyValues)   / count($dailyValues),   2));
            $data['weekly'][]   = array($timestamp*1000, round(array_sum($weeklyValues)  / count($weeklyValues),  2));
            $data['monthly'][]  = array($timestamp*1000, round(array_sum($monthlyValues) / count($monthlyValues), 2));
            $data['cnt'][]      = array($timestamp*1000, (int)$listingEntry['quantity']);
            $data['daily_cnt'][]= array($timestamp*1000, round(array_sum($dailyCntValues) / count($dailyCntValues),   2));
        }

        return $data;
    }

    public static function getChartDatasetData(\ModelCriteria $q) {
        $data = array(
        	'raw'     => array(),
        	'daily'   => array(),
        	'weekly'  => array(),
        	'monthly' => array(),
        );

        $cache = self::getGemCache();

        $rates = $q->select(array('rateDatetime', 'rate'))
                   ->filterByRateDatetime(date("Y-m-d 00:00:00", strtotime("-1 week")), \Criteria::GREATER_EQUAL)
                   ->find();

        /*
         * use these 3 arrays to maintain the values over which we calculate the moving average
        *  every value is added to the array, but we pop off values older then the specified threshold (day, week, month)
        */
        $dailyValues   = array();
        $weeklyValues  = array();
        $monthlyValues = array();
        foreach ($rates as $rateEntry) {
            $date = new DateTime("{$rateEntry['rateDatetime']}");
            $date->setTimezone(new DateTimeZone('UTC'));
            $timestamp = $date->getTimestamp();

            $dailyValues[$timestamp]   = $rateEntry['rate'];
            $weeklyValues[$timestamp]  = $rateEntry['rate'];
            $monthlyValues[$timestamp] = $rateEntry['rate'];
            $rateEntry['rate'] = round($rateEntry['rate'], 2);


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

            $data['raw'][]     = array($timestamp*1000, $rateEntry['rate']);
            $data['daily'][]   = array($timestamp*1000, round(array_sum($dailyValues)   / count($dailyValues),   2));
            $data['weekly'][]  = array($timestamp*1000, round(array_sum($weeklyValues)  / count($weeklyValues),  2));
            $data['monthly'][] = array($timestamp*1000, round(array_sum($monthlyValues) / count($monthlyValues), 2));
        }

        return $data;
    }
}
