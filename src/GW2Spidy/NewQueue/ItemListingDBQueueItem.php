<?php

namespace GW2Spidy\NewQueue;

use \DateTime;
use \Exception;
use GW2Spidy\TradingPostSpider;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\BuyListingQuery;
use GW2Spidy\DB\SellListingQuery;

use GW2Spidy\Util\RedisQueue\RedisPriorityIdentifierQueueItem;

class ItemListingDBQueueItem {
    const ONE_DAY = 86400;
    const ONE_HOUR = 3600;
    const THREE_HOURS = 10900;
    const FIFTEEN_MIN = 900;
    const FIVE_MIN = 300;

    protected $item;

    public function __construct($input) {
        if ($input instanceof Item) {
            $this->item = $input;
        } else {
            $this->item = ItemQuery::create()->findPk($input);
        }
    }

    public function getIdentifier() {
        return $this->item->getDataId();
    }

    public function getItem() {
        return $this->item;
    }

    public function getPriority() {
        return time() + $this->getItemPriority();
    }

    public function getItemPriority() {
        if ($this->item->getItemTypeId() == null) {
            return 24 * 60 * 60;
        }

        switch ($this->item->getItemType()->getTitle()) {
            case 'Weapon':
            case 'Armor':
                if ($this->item->getRarity() >= 3) {
                    if ($this->item->getRestrictionLevel() > 60) {
                        return self::FIFTEEN_MIN;
                    } else if ($this->item->getRestrictionLevel() > 40) {
                        return self::ONE_HOUR;
                    } else {
                        return self::THREE_HOURS;
                    }
                } else if ($this->item->getRarity() >= 2) {
                    return self::THREE_HOURS;
                } else {
                    return self::ONE_DAY;
                }

                break;

            case 'Gathering':
            case 'Tool':
                return self::ONE_DAY;

                break;

            case 'Trophy':
                if ($this->item->getRarity() >= 2) {
                    return self::FIFTEEN_MIN;
                } else {
                    return self::ONE_DAY;
                }

                break;

            case 'Gizmo':
                if ($this->item->getRarity() >= 5) {
                    return self::ONE_HOUR;
                } else {
                    return self::THREE_HOURS;
                }

                break;

            case 'Mini':
            case 'Bag':
            case 'Crafting Material':
                return self::FIFTEEN_MIN;

                break;

            case 'Container':
                if ($this->item->getRarity() >= 2) {
                    return self::FIFTEEN_MIN;
                } else {
                    return self::ONE_HOUR;
                }

                break;

            case 'Consumable':
            case 'Upgrade Component':
            case 'Trinket':
                return self::ONE_HOUR;

                break;

            default:
                throw new Exception("Unknown type {$this->item->getItemType()->getTitle()}");

                break;
        }
    }
}

?>