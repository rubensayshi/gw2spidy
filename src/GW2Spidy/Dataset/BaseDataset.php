<?php

namespace GW2Spidy\Dataset;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use GW2Spidy\Util\CacheHandler;
use GW2Spidy\DB\GoldToGemRateQuery;
use GW2Spidy\DB\GemToGoldRateQuery;

abstract class BaseDataset {
    /*
     * just easy constants to make the code more readable
     */
    const TS_ONE_HOUR = 3600;
    const TS_ONE_DAY  = 86400;
    const TS_ONE_WEEK = 604800;

    /**
     * var to keep track what we last updated
     *  when doing a new update we can continue from this point
     * @var DateTime $lastUpdated
     */
    protected $lastUpdated = null;

    /**
     * var to make sure we only update the dataset once per request
     * @var boolean $updated
     */
    protected $updated = false;

    /**
     * boolean to check weither or not the dataset is completely up-to-date
     *
     * @var boolean $uptodate
     */
    public $uptodate = false;

    /**
     * threshold from where we start aggregating by hour
     *
     * @var  int    $hourlyThreshold
     */
    protected $hourlyThreshold = self::TS_ONE_DAY;

    /*
     * final datasets used for output
     */
    protected $noMvAvg         = array();
    protected $dailyMvAvg      = array();

    /**
     * the timestamps grouped by their hour
     * @var $tsByHour
     */
    protected $tsByHour = array();

    /*
     * temporary datasets to use when replacing ticks with their hourly average
     */
    protected $hourlyNoMvAvg     = array();
    protected $hourlyDailyMvAvg  = array();

    /*
     * cache datasets to avoid having to filter the whole dataset all the time
     */
    protected $past24Hours = array();
    protected $pastWeek    = array();

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
     * update the current dataset with new values from the database
     *  if posible only with values since our lastUpdated moment
     *
     * when implementing this it should loop over all to-be-added ticks (in asc order)
     *  and call processTick
     * like ItemDataset and GemExchangeDataset do
     */
    abstract protected function updateDataset();

    /**
     * process a single tick,
     *  adding it to the different lines and cleaning up / aggregating old data
     *
     * @param  DateTime    $date
     * @param  int         $rate
     */
    protected function processTick(DateTime $date, $rate) {
        $ts   = $date->getTimestamp();
        $tsHr = self::tsHour($ts);

        // get the previous tick
        end($this->noMvAvg);
        $prevTs = key($this->noMvAvg);

        // add to noMvAvg
        $this->tsByHour[$tsHr][] = $ts;
        $this->noMvAvg[$ts] = array($ts * 1000, $rate);

        // add to past 24 hours
        $this->past24Hours[$ts] = $rate;

        /*
         * we process the gap between the previous tick and our tick
         * since everything before the previous tick has already been processed!
         */
        if ($prevTs) {
            /*
             * remove ticks from the past24Hours cache if they're older then 24 hours
             *  but younger then what the previous tick should have already removed
             */
            $thresMin = self::tsHour($prevTs - self::TS_ONE_DAY);
            $thresMax = self::tsHour($ts - self::TS_ONE_DAY);
            while ($thresMin < $thresMax) {
                $thisTsHour = self::tsHour($thresMin);
                $thisHour   = array();

                if (isset($this->tsByHour[$thisTsHour])) {
                    foreach (array_unique($this->tsByHour[$thisTsHour]) as $tickTs) {
                        unset($this->past24Hours[$tickTs]);
                    }
                }

                $thresMin += self::TS_ONE_HOUR;
            }

            /*
             * aggregate ticks older then 24 hours into 1 tick per hour (averaged out for that hour)
             *  this is done to reduce the size of the the dataset we have in memory
             *  however since we're now also cleaning up our dataset in the database we can disable this
             */
            if (getAppConfig('gw2spidy.aggregate_ticks_on_request')) {
                $thresMin = self::tsHour($prevTs - $this->hourlyThreshold);
                $thresMax = self::tsHour($ts - $this->hourlyThreshold);
                while ($thresMin < $thresMax) {
                    $thisTsHour = self::tsHour($thresMin);
                    $thisHour   = array();

                    if (isset($this->tsByHour[$thisTsHour])) {
                        // (re)calculate the average of this ticks hour
                        $hourNoMvAvg = array();
                        $hourDailyMvAvg = array();
                        foreach ($this->tsByHour[$thisTsHour] as $tickTs) {
                            $hourNoMvAvg[] = $this->noMvAvg[$tickTs][1];
                            $hourDailyMvAvg[] = $this->dailyMvAvg[$tickTs][1];
                        }
                        $this->hourlyNoMvAvg[$thisTsHour] = array_sum($hourNoMvAvg) / count($hourNoMvAvg);
                        $this->hourlyDailyMvAvg[$thisTsHour] = array_sum($hourDailyMvAvg) / count($hourDailyMvAvg);

                        // remove old ticks
                        foreach (array_unique($this->tsByHour[$thisTsHour]) as $tickTs) {
                            unset($this->noMvAvg[$tickTs]);
                            unset($this->dailyMvAvg[$tickTs]);
                        }

                        // insert hourly ticks
                        $this->noMvAvg[$thisTsHour] = array($thisTsHour * 1000, $this->hourlyNoMvAvg[$thisTsHour]);
                        $this->dailyMvAvg[$thisTsHour] = array($thisTsHour * 1000, $this->hourlyDailyMvAvg[$thisTsHour]);
                        $this->noMvAvg[$thisTsHour] = array($thisTsHour * 1000, $this->hourlyNoMvAvg[$thisTsHour]);
                        $this->tsByHour[$thisTsHour] = array($thisTsHour);
                    }

                    $thresMin += self::TS_ONE_HOUR;
                }
            }
        }

        // calculate new daily mv avg tick
        if (count($this->past24Hours)) {
            $dailyMvAvg = array_sum($this->past24Hours) / count($this->past24Hours);
            $this->dailyMvAvg[$ts] = array($ts * 1000, $dailyMvAvg);
        }
    }

    public function getNoMvAvgDataForChart() {
        $this->updateDataset();

        ksort($this->noMvAvg);

        return array_values($this->noMvAvg);
    }

    public function getDailyMvAvgDataForChart() {
        $this->updateDataset();

        ksort($this->dailyMvAvg);

        return array_values($this->dailyMvAvg);
    }

    /**
     * clean up any interal cache vars we had
     *  and mark updated = false so next time the object is retrieved from cache it will be updated again
     */
    public function __wakeup() {
        $this->hourlyDailyMvAvg = array();
        $this->hourlyNoMvAvg = array();
        $this->updated = false;
    }
}
