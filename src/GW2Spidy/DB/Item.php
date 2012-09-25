<?php

namespace GW2Spidy\DB;

use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseItem;
use GW2Spidy\Util\CacheHandler;


/**
 * Skeleton subclass for representing a row from the 'item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class Item extends BaseItem {
    const RARITY_COMMON     = 1;
    const RARITY_FINE       = 2;
    const RARITY_MASTERWORK = 3;
    const RARITY_RARE       = 4;
    const RARITY_EXOTIC     = 5;
    const RARITY_LEGENDARY  = 6;
    const FALSE_POSITIVE = 'FALSE_POSITIVE';

    public function getRarityName() {
        switch ($this->getRarity()) {
            case self::RARITY_COMMON:     return "Common";
            case self::RARITY_FINE:       return "Fine";
            case self::RARITY_MASTERWORK: return "Masterwork";
            case self::RARITY_RARE:       return "Rare";
            case self::RARITY_EXOTIC:     return "Exotic";
            case self::RARITY_LEGENDARY:  return "Legendary";
            default:                      return $this->getRarityName() ?: "Rarity [{$this->getRarity()}]";
        }
    }

    public function getGW2DBTooltip() {
        $cache = CacheHandler::getInstance('item_gw2db_tooltips');

        if (!($tooltip = $cache->get($this->getDataId()))) {
            $tooltip = $this->getGW2DBTooltipFromGW2DB();

            $cache->set($this->getDataId(), $tooltip);
        }

        return $tooltip;
    }

    public function getGW2DBTooltipFromGW2DB() {
        $js = file_get_contents("http://www.gw2db.com/items/{$this->getGW2DBExternalId()}/tooltip?x&advanced=1&callback=WP_OnTooltipLoaded&_=1348564347696");


        $js = preg_replace("/^WP_OnTooltipLoaded\(/", '', $js);
        $js = preg_replace("/\)$/", '', $js);

        $data = json_decode($js, true);
        $html = $data['Tooltip'];

        return stripslashes($html);
    }

    public function getMargin() {
	$margin = intval($this->getMinSaleUnitPrice() * 0.85 - $this->getMaxOfferUnitPrice());
	if($this->getMaxOfferUnitPrice() == 0 | $this->getMinSaleUnitPrice() == 0)
	{
		$margin = 0;
	}

   	$margin = ($margin > 0) ? $margin : 0;
	return $margin;

    }


   public function getRarityCSSClass() {
        return strtolower("rarity-" . str_replace(" ", "-", $this->getRarityName()));
    }

    /**
     * Get the associated ItemSubType object
     *
     * @param      PropelPDO   $con    Optional Connection object.
     * @return     ItemSubType         The associated ItemSubType object.
     * @throws     PropelException
     */
    public function getItemSubType(PropelPDO $con = null) {
        if ($this->aItemSubType === null && ($this->item_sub_type_id !== null)) {
            $cacheKey            = __CLASS__ . "::" . __METHOD__ . "::" . $this->getDataId();
            $this->aItemSubType  = ApplicationCache::getInstance()->get($cacheKey);

            if ($this->aItemSubType == static::FALSE_POSITIVE) {
                $this->aItemSubType = null;
            } else if (!$this->aItemSubType) {
                $this->aItemSubType = ItemSubTypeQuery::create()
                    ->filterByItem($this)
                    ->filterByMainTypeId($this->getItemTypeId())
                    ->findOne($con);

                ApplicationCache::getInstance()->set($cacheKey, $this->aItemSubType ?: static::FALSE_POSITIVE, MEMCACHE_COMPRESSED, 86400);
            }
        }

        return $this->aItemSubType;
    }

    public function __toString() {
        return $this->getName();
    }

} // Item
