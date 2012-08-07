<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\ApplicationCache;

use GW2Spidy\Util\CacheHandler;

use GW2Spidy\DB\om\BaseItemTypeQuery;


/**
 * Skeleton subclass for performing query and update operations on the 'item_type' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class ItemTypeQuery extends BaseItemTypeQuery {
    public static function getAllTypes() {
        $cacheKey = __CLASS__ . "::" . __METHOD__;
        $types    = ApplicationCache::getInstance()->get($cacheKey);

        if (!$types) {
            $types = self::create()
                ->orderByTitle()
                ->find();

            ApplicationCache::getInstance()->set($cacheKey, $types, MEMCACHE_COMPRESSED, 86400);
        }

        return $types;
    }
} // ItemTypeQuery
