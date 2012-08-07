<?php

namespace GW2Spidy\DB;

use GW2Spidy\DB\om\BaseItemSubType;


/**
 * Skeleton subclass for representing a row from the 'item_sub_type' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class ItemSubType extends BaseItemSubType {
    public function __toString() {
        return $this->getTitle();
    }
} // ItemSubType
