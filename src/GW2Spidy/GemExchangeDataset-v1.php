<?php

namespace GW2Spidy;

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
class GemExchangeDatasetv1 {
    const TYPE_GOLD_TO_GEM = 'gold_to_gem';
    const TYPE_GEM_TO_GOLD = 'gem_to_gold';
    const DATE_KEY_FORMAT       = 'YmdHis';
    const DATE_KEY_FORMAT_HOUR  = 'YmdH';
    const DATE_KEY_FORMAT_DAY   = 'Ymd';
    const DATE_KEY_FORMAT_WEEK  = 'YW';
    const DATE_KEY_FORMAT_MONTH = 'Ym';

    protected $type;
    protected $lastUpdated = null;
    protected $updated = false;

    protected $raw    = array();
    protected $hourly = array();
    protected $daily  = array();

    protected $hours = array();
    protected $days  = array();

    protected $dailyMvAvg  = array(); // 1 tick per hour
    protected $weeklyMvAvg = array(); // 1 tick per day

    public function __construct($type) {
        $this->type = $type;
    }

    protected function updateDataset() {
        if ($this->updated) {
            return;
        }

        $end   = null;
        $start = $this->lastUpdated;

        $q = $this->type == self::TYPE_GEM_TO_GOLD ? GemToGoldRateQuery::create() : GoldToGemRateQuery::create();
        $q->select(array('rateDatetime', 'rate'));

        $q->filterByRateDatetime(date("Y-m-d 00:00:00", strtotime("-2weeks")), \Criteria::GREATER_EQUAL);
        if ($start) {
            $q->filterByRateDatetime($start, \Criteria::GREATER_THAN);
        }

        $q->orderByRateDatetime(\Criteria::ASC);

        $rates = $q->find();

        // we need to keep track of the stuff we touch, they need to be recalculated
        $hoursTouched  = array();
        $daysTouched   = array();

        foreach ($rates as $rateEntry) {
            $rate     = $rateEntry['rate'];
            $date     = new DateTime("{$rateEntry['rateDatetime']}");
            $key      = $date->format(self::DATE_KEY_FORMAT);
            $hourKey  = $date->format(self::DATE_KEY_FORMAT_HOUR);

            // for a fresh instance we need to fix this
            if (!$start) {
                $start = clone $date;
            }

            $this->raw[$key] = $rate;
            $this->hours[$hourKey][$key] = $rate;

            // track the hour we touched
            $hoursTouched[] = $hourKey;
        }

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

    public function getRawDataForChart() {
        $this->updateDataset();

        return $this->raw;
    }

    public function getDailyMvAvgDataForChart() {
        $this->updateDataset();

        return $this->dailyMvAvg;
    }

    public function getWeeklyMvAvgDataForChart() {
        $this->updateDataset();

        return $this->weeklyMvAvg;
    }

    public function __wakeup() {
        $this->updated = false;
    }
}
