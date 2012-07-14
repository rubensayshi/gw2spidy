<?php

namespace GW2Spidy\TradeMarket;

class Item {
    public $type_id;
    public $data_id;
    public $name;
    public $description;
    public $gem_store_description;
    public $gem_store_blurb;
    public $restriction_level;
    public $rarity;
    public $vendor_sell_price;
    public $max_offer_unit_price;
    public $offer_availability;
    public $min_sale_unit_price;
    public $sale_availability;
    public $img;
    public $rarity_word;

    /**
     * @return Item
     */
    public static function fromStdObject($object) {
        $item = new Item();

        foreach ((array)$object as $k => $v) {
            if (property_exists($item, $k)) {
                $item->$k = $v;
            }
        }

        return $item;
    }

    /**
     * @return Item
     */
    public static function getByExactName($name) {
        return TradeMarket::getInstance()->getItemByExactName($name);
    }

    public function getId() {
        return $this->data_id;
    }

    public function getSellListings() {
        return $this->getListings('sells');
    }

    public function getListings($type = 'sells') {
        return TradeMarket::getInstance()->getListingsById($this->data_id, $type);
    }
}

?>