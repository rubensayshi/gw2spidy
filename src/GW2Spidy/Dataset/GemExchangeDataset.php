<?php

namespace GW2Spidy\Dataset;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use GW2Spidy\Util\CacheHandler;
use GW2Spidy\DB\GoldToGemRateQuery;
use GW2Spidy\DB\GemToGoldRateQuery;

/**
 * daily average line should be 1 tick per hour
 * weekly and monthly
 *
 */
class GemExchangeDataset {
    const TYPE_GOLD_TO_GEM = 'gold_to_gem';
    const TYPE_GEM_TO_GOLD = 'gem_to_gold';
    const DATE_INT_FORMAT       = 'YmdHis';
    const DATE_INT_FORMAT_HOUR  = 'YmdH';
    const DATE_INT_FORMAT_DAY   = 'Ymd';
    const DATE_INT_FORMAT_WEEK  = 'YW';
    const DATE_INT_FORMAT_MONTH = 'Ym';

    protected $type;
    protected $lastUpdated = null;
    protected $updated = false;

    protected $noMvAvg         = array();
    protected $dailyMvAvg      = array(); // 1 tick per hour
    protected $tsByHour        = array();

    protected $hourlyNoMvAvg    = array();
    protected $hourlyDailyMvAvg = array();

    protected $past24Hours     = array();

    protected $weeklyMvAvg = array(); // 1 tick per hour

    public function __construct($type) {
        $this->type = $type;
    }

    public static function tsHour($ts) {
        return ceil($ts / 3600) * 3600;
    }
    public static function tsDay($ts) {
        return ceil($ts / 86400) * 86400;
    }

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

        // remove ticks from the past24hour cache
        // and replace all ticks older then 24 hours with just 1 (averaged) tick per hour
        //  but we process this from the previous tick onwards, saves a lot of looping since we already did it for the previous tick
        if ($prevTs) {
            $thresMin = self::tsHour($prevTs - 86400);
            $thresMax = self::tsHour($ts - 86400);

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
                    $this->hourlyNoMvAvg[$thisTsHour] =array_sum($hourNoMvAvg) / count($hourNoMvAvg);
                    $this->hourlyDailyMvAvg[$thisTsHour] =array_sum($hourDailyMvAvg) / count($hourDailyMvAvg);

                    foreach (array_unique($this->tsByHour[$thisTsHour]) as $tickTs) {
                        unset($this->past24Hours[$tickTs]);
                        unset($this->noMvAvg[$tickTs]);
                        unset($this->dailyMvAvg[$tickTs]);
                    }

                    $this->noMvAvg[$thisTsHour] = array($thisTsHour * 1000, $this->hourlyNoMvAvg[$thisTsHour]);
                    $this->dailyMvAvg[$thisTsHour] = array($thisTsHour * 1000, $this->hourlyDailyMvAvg[$thisTsHour]);
                    $this->tsByHour[$thisTsHour] = array($thisTsHour);
                }

                $thresMin += 3600;
            }
        }

        // calculate new mv avg tick
        if (count($this->past24Hours)) {
            $dailyMvAvg = array_sum($this->past24Hours) / count($this->past24Hours);
            $this->dailyMvAvg[$ts] = array($ts * 1000, $dailyMvAvg);
        }
    }

    public function updateDataset() {
        if ($this->updated) {
            return;
        }

        $t = microtime(true);

        $end   = null;
        $start = $this->lastUpdated;

        $q = $this->type == self::TYPE_GEM_TO_GOLD ? GemToGoldRateQuery::create() : GoldToGemRateQuery::create();
        $q->select(array('rateDatetime', 'rate'));

        if ($start) {
            $q->filterByRateDatetime($start, \Criteria::GREATER_THAN);
        }

        // $q->filterByRateDatetime('2012-10-12 00:00:00', \Criteria::LESS_THAN);

        $q->orderByRateDatetime(\Criteria::ASC);

        $rates = $q->find();

        foreach ($rates as $rateEntry) {
            $date = new DateTime("{$rateEntry['rateDatetime']}");
            $rate = intval($rateEntry['rate']);

            $end = $date;

            $this->processTick($date, $rate);
        }

        if ($end) {
            $this->lastUpdated = $end;
        }

        return;

        $end = end($rates);
        $end = new DateTime("{$end['rateDatetime']}");

        // go over the hours we touched and process them
        foreach ($hoursTouched as $key) {
            $date   = DateTime::createFromFormat(self::DATE_KEY_FORMAT_HOUR, $key);
            $dayKey = $date->format(self::DATE_KEY_FORMAT_DAY);
            $hourAverage = array_sum($this->hours[$key]) / count($this->hours[$key]);

            $this->hourly[$key] = $hourAverage;
            $this->days[$dayKey][$key] = $hourAverage;

            // track the day we touched
            $daysTouched[] = $dayKey;
        }

        // go over the days we touched and process them
        foreach ($daysTouched as $key) {
            $date     = DateTime::createFromFormat(self::DATE_KEY_FORMAT_DAY, $key);
            $weekKey  = $date->format(self::DATE_KEY_FORMAT_WEEK);
            $monthKey = $date->format(self::DATE_KEY_FORMAT_MONTH);
            $dayAverage = array_sum($this->days[$key]) / count($this->days[$key]);

            $this->daily[$key] = $dayAverage;
        }

        for ($i = $start->format(self::DATE_KEY_FORMAT_HOUR); $i <= $end->format(self::DATE_KEY_FORMAT_HOUR); $i++) {
            $date    = DateTime::createFromFormat(self::DATE_KEY_FORMAT_HOUR, $i);
            $date->add(new DateInterval('PT1H'));
            $pastDay = array_filter(array_slice($this->hourly, -24));
            $pastHourAvg = array_sum($pastDay) / count($pastDay);
            $this->dailyMvAvg[] = array($date->getTimestamp() * 1000, intval($pastHourAvg));

            if (!isset($this->hours[$i])) {
                $this->hours[$i] = null;
            }
        }

        for ($i = $start->format(self::DATE_KEY_FORMAT_DAY); $i <= $end->format(self::DATE_KEY_FORMAT_DAY); $i++) {
            $date     = DateTime::createFromFormat(self::DATE_KEY_FORMAT_DAY, $i);
            $date->add(new DateInterval('P1D'));
            $pastWeek = array_filter(array_slice($this->daily, -7));
            $pastWeekAvg = array_sum($pastWeek) / count($pastWeek);
            $this->weeklyMvAvgMvAvg[] = array($date->getTimestamp() * 1000, intval($pastWeekAvg));

            if (!isset($this->days[$i])) {
                $this->days[$i] = null;
            }
        }

        $this->hours = array_slice($this->hours, -24, 1, true);
        $this->days  = array_slice($this->days,  -7, 1, true);
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

    public function getWeeklyMvAvgDataForChart() {
        $this->updateDataset();

        return $this->weeklyMvAvg;
    }

    public function __wakeup() {
        $this->updated = false;
    }
}
