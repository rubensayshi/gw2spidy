<?php

namespace GW2Spidy\Dataset;

use GW2Spidy\Application;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use GW2Spidy\Util\CacheHandler;
use GW2Spidy\DB\GoldToGemRateQuery;
use GW2Spidy\DB\GemToGoldRateQuery;

class GemExchangeDataset extends BaseDataset {
    /*
     * different posible type of datasets we can have
     */
    const TYPE_GOLD_TO_GEM = 'gold_to_gem';
    const TYPE_GEM_TO_GOLD = 'gem_to_gold';

    /**
     * one of the self::TYPE_ constants
     * @var $type
     */
    protected $type;

    /**
     * @param  string    $type        should be one of self::TYPE_
     */
    public function __construct($type) {
        $this->type = $type;
    }

    /**
     * update the current dataset with new values from the database
     *  if posible only with values since our lastUpdated moment
     */
    public function updateDataset() {
        if ($this->updated) {
            return;
        }

        $limit = 5000;
        $end   = null;
        $start = $this->lastUpdated;

        $q = $this->type == self::TYPE_GEM_TO_GOLD ? GemToGoldRateQuery::create() : GoldToGemRateQuery::create();
        $q->select(array('rateDatetime', 'rate'));

        // only retrieve new ticks since last update
        if ($start) {
            $q->filterByRateDatetime($start, \Criteria::GREATER_THAN);
        }

        // fake 5 days ago so we can test new ticks being added
        // $fake = new DateTime();
        // $fake->sub(new DateInterval('P5D'));
        // $q->filterByRateDatetime($fake, \Criteria::LESS_THAN);

        // ensure ordered data, makes our life a lot easier
        $q->orderByRateDatetime(\Criteria::ASC);

        // limit so on a complete cache flush we can ease into building up the cache again
        $q->limit($limit);

        // loop and process ticks
        $rates = $q->find();
        foreach ($rates as $rateEntry) {
            $date = new DateTime("{$rateEntry['rateDatetime']}");
            $rate = intval($rateEntry['rate']);

            $end = $date;

            $this->processTick($date, $rate);
        }

        if (!($this->uptodate = count($rates) != $limit)) {
            $app = Application::getInstance();
            $app['no_cache'] = true;
        }

        // update for next time
        $this->updated = true;
        if ($end) {
            $this->lastUpdated = $end;
        }
    }
}
