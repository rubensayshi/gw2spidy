<?php

namespace GW2Spidy\Dataset;

use GW2Spidy\DB\GoldToGemRateQuery;

use GW2Spidy\DB\GemToGoldRateQuery;

use GW2Spidy\DB\GoldToGemRate;

use GW2Spidy\DB\GemToGoldRate;

use \DateTime;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

class GemDatasetCleaner {
    /*
     * different posible type of datasets we can have
     */
    const TYPE_GOLD_TO_GEM = 'gold_to_gem';
    const TYPE_GEM_TO_GOLD = 'gem_to_gold';

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
    public function __construct($type) {
        $this->type = $type;
    }

    public function clean($cleanup = self::CLEANUP_WEEK) {
        $thres = self::tsHour(time() - ($cleanup == self::CLEANUP_WEEK ? self::TS_ONE_WEEK : self::TS_ONE_MONTH));

        $count = 0;
        $con = \Propel::getConnection();

        $table = $this->type == self::TYPE_GEM_TO_GOLD ? 'gem_to_gold_rate' : 'gold_to_gem_rate';
        $stmt = $con->prepare("
                SELECT
                rate_datetime AS rateDatetime, rate, volume
                FROM {$table}
                ORDER BY rate_datetime ASC");

        $stmt->execute();
        $rates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ticks = array();
        $tsGrpd = array();
        foreach ($rates as $rate) {
            $date  = new DateTime("{$rate['rateDatetime']}");
            $ts    = $date->getTimestamp();
            if ($cleanup == self::CLEANUP_WEEK) {
                $grpDate = new DateTime($date->format("Y-m-d H:00:00"));
            } else {
                $grpDate = new DateTime($date->format("Y-m-d 00:00:00"));
            }
            $tsGrp = $grpDate->getTimestamp();

            $ticks[$ts] = $rate;
            $tsGrpd[$tsGrp][] = $ts;
        }

        foreach ($tsGrpd as $tsGrp => $tss) {
            if ($tsGrp >= $thres) {
                break;
            }

            if (count($tss) <= 1) {
                continue;
            }

            $grpRates = array();
            $grpVolumes = array();

            foreach ($tss as $ts) {
                $grpRates[] = $ticks[$ts]['rate'];
                $grpVolumes[] = $ticks[$ts]['volume'];
            }

            $grpRAvg = array_sum($grpRates) / count($grpRates);
            $grpVAvg = array_sum($grpVolumes) / count($grpVolumes);

            $con->beginTransaction();


            $q = $this->type == self::TYPE_GEM_TO_GOLD ? new GemToGoldRateQuery() : new GoldToGemRateQuery();
            $q->filterByRateDatetime($tss, \Criteria::IN)
              ->delete();

            $new = $this->type == self::TYPE_GEM_TO_GOLD ? new GemToGoldRate() : new GoldToGemRate();
            $new->setRateDatetime($tsGrp);
            $new->setRate($grpRAvg);
            $new->setVolume($grpVAvg);
            $new->save();

            $con->commit();
            $count++;
        }

        unset($ticks, $tsByHour);

        return $count;
    }
}
