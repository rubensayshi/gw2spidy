<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseItemType;


/**
 * Skeleton subclass for representing a row from the 'item_type' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class ItemType extends BaseItemType {
    protected $displaySubTypes = null;

    public function getSubTypes($criteria = null, PropelPDO $con = null) {
        $cacheKey = __CLASS__ . "::" . __METHOD__ . "::" . $this->getId();
        $subtypes = ApplicationCache::getInstance()->get($cacheKey);

        if (!$subtypes) {
            $subtypes = parent::getSubTypes($criteria, $con);

            ApplicationCache::getInstance()->set($cacheKey, $subtypes, MEMCACHE_COMPRESSED, 86400);
        }

        return $subtypes;
    }

    public function getDisplaySubTypes() {
        if (is_null($this->displaySubTypes)) {
            $this->displaySubTypes = $this->getSubTypes();
            foreach ($this->displaySubTypes as $k => $subtype) {
                if (!$subtype->getTitle()) {
                    unset($this->displaySubTypes[$k]);
                }
            }
        }

        return $this->displaySubTypes;
    }

    public function __toString() {
        return $this->getTitle();
    }
} // ItemType
