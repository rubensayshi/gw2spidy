<?php

namespace GW2Spidy\DB;

use GW2Spidy\Application;

use GW2Spidy\Util\Functions;

use GW2Spidy\Util\ApplicationCache;
use GW2Spidy\DB\om\BaseItem;
use GW2Spidy\Util\CacheHandler;

use GW2Spidy\GW2API\APIItem;

use \PropelPDO;

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
    const PRIO_ONE   = 1;
    const PRIO_TWO   = 2;
    const PRIO_THREE = 3;
    const PRIO_FOUR  = 5;
    const PRIO_FIVE  = 10;

    const RARITY_JUNK       = 0;
    const RARITY_COMMON     = 1;
    const RARITY_FINE       = 2;
    const RARITY_MASTERWORK = 3;
    const RARITY_RARE       = 4;
    const RARITY_EXOTIC     = 5;
    const RARITY_ASCENDED  = 6;
    const RARITY_LEGENDARY  = 7;
    const FALSE_POSITIVE = 'FALSE_POSITIVE';

    /* Type ids taken from the database */
    const TYPE_ID_UPGRADE_COMPONENT = 17;
    const TYPE_ID_CONTAINER = 4;
    const TYPE_ID_GIZMO = 7;
    const TYPE_ID_CRAFTING_MATERIAL = 5;
    const TYPE_ID_MINI = 11;
    const TYPE_ID_ARMOR = 0;
    const TYPE_ID_GATHERING = 6;
    const TYPE_ID_TRINKET = 15;
    const TYPE_ID_BAG = 2;
    const TYPE_ID_TROPHY = 16;
    const TYPE_ID_CONSUMABLE = 3;
    const TYPE_ID_WEAPON = 18;
    const TYPE_ID_TOOL = 13;

    public function getBestPrice() {
        if ($this->getVendorPrice()) {
            return $this->getVendorPrice();
        } else {
            return $this->getMinSaleUnitPrice();
        }
    }

    public function setName($v) {
        $this->setCleanName($this->cleanName($v));

        return parent::setName($v);
    }

    public function setTpName($v) {
        $this->setCleanTpName($this->cleanName($v));

        return parent::setTpName($v);
    }

    protected function cleanName($v) {
        $v = str_replace('[s]', '', $v);

        return $v;
    }

    public function preSave(PropelPDO $con = null) {
        if ($this->isColumnModified(ItemPeer::MIN_SALE_UNIT_PRICE) || $this->isColumnModified(ItemPeer::MAX_OFFER_UNIT_PRICE)) {
            $this->setLastPriceChanged(new \DateTime());
        }

        return true;
    }

    public function getQueuePriority() {
        if ($this->getItemTypeId() === null) {
            return self::PRIO_FIVE;
        }

        switch ($this->getItemTypeId()) {
            case self::TYPE_ID_WEAPON:
            case self::TYPE_ID_ARMOR:
                if ($this->getRarity() >= 3) {
                    if ($this->getRestrictionLevel() > 60) {
                        return self::PRIO_ONE;
                    } else if ($this->getRestrictionLevel() > 40) {
                        return self::PRIO_TWO;
                    } else {
                        return self::PRIO_THREE;
                    }
                } else if ($this->getRarity() >= 2) {
                    return self::PRIO_THREE;
                } else {
                    return self::PRIO_FIVE;
                }

                break;

            case self::TYPE_ID_GATHERING:
            case self::TYPE_ID_TOOL:
                return self::PRIO_FIVE;

                break;

            case self::TYPE_ID_TROPHY:
                if ($this->getRarity() >= 2) {
                    return self::PRIO_ONE;
                } else {
                    return self::PRIO_FIVE;
                }

                break;

            case self::TYPE_ID_GIZMO:
                if ($this->getRarity() >= 5) {
                    return self::PRIO_TWO;
                } else {
                    return self::PRIO_THREE;
                }

                break;

            case self::TYPE_ID_MINI:
            case self::TYPE_ID_BAG:
            case self::TYPE_ID_CRAFTING_MATERIAL:
                return self::PRIO_ONE;

                break;

            case self::TYPE_ID_CONTAINER:
                if ($this->getRarity() >= 2) {
                    return self::PRIO_ONE;
                } else {
                    return self::PRIO_TWO;
                }

                break;

            case self::TYPE_ID_CONSUMABLE:
            case self::TYPE_ID_UPGRADE_COMPONENT:
            case self::TYPE_ID_TRINKET:
                return self::PRIO_TWO;

                break;

            default:
                throw new \Exception("Unknown type {$this->getItemType()->getTitle()}");

                break;
        }
    }

    public function getRarityName() {
        switch ($this->getRarity()) {
            case self::RARITY_JUNK:       return "Junk";
            case self::RARITY_COMMON:     return "Common";
            case self::RARITY_FINE:       return "Fine";
            case self::RARITY_MASTERWORK: return "Masterwork";
            case self::RARITY_RARE:       return "Rare";
            case self::RARITY_EXOTIC:     return "Exotic";
            case self::RARITY_ASCENDED:   return "Ascended";
            case self::RARITY_LEGENDARY:  return "Legendary";
            default:                      return "Rarity [{$this->getRarity()}]";
        }
    }
    
    public function getTooltip() {
        $APIItem = APIItem::getItemById($this->getDataId());
        
        return ($APIItem !== null) ? $APIItem->getTooltip() : null;
    }

    public function getMargin() {
		$margin = intval($this->getMinSaleUnitPrice() * 0.85 - $this->getMaxOfferUnitPrice());
		if($this->getMaxOfferUnitPrice() == 0 || $this->getMinSaleUnitPrice() == 0 || $margin <= 0)
			return 0;

		return $margin;

    }

    public function getIngameCode() {
        $code = chr(2);
        $code .= chr(1);
        $code .= chr($this->getDataId() % 256);
        $code .= chr(floor($this->getDataId() / 256)) . chr(0) . chr(0);

        $code = base64_encode($code);

        return "[&{$code}]";
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
