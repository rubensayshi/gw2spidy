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
        $con = \Propel::getConnection();

        $table = $this->type == self::TYPE_GEM_TO_GOLD ? 'gem_to_gold_rate' : 'gold_to_gem_rate';

        $where = "";
        // only retrieve new ticks since last update
        if ($start) {
            $where = " WHERE rate_datetime > '{$start->format('Y-m-d H:i:s')}'";
        }

        $stmt = $con->prepare("
                SELECT
                rate_datetime AS rateDatetime,
                rate
                FROM {$table}
                {$where}
                ORDER BY rate_datetime ASC
                LIMIT {$limit}");

        $stmt->execute();
        $rates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
