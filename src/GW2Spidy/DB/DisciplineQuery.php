<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseDisciplineQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'discipline' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class DisciplineQuery extends BaseDisciplineQuery {
    public static function getAllDisciplines() {
        $cacheKey = __CLASS__ . "::" . __METHOD__;
        $all      = ApplicationCache::getInstance()->get($cacheKey);

        if (!$all) {
            $all = self::create()
                ->orderByName()
                ->find();

            ApplicationCache::getInstance()->set($cacheKey, $all, MEMCACHE_COMPRESSED, 86400);
        }

        return $all;
    }
} // DisciplineQuery
