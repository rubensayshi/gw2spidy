<?php

namespace GW2Spidy\GW2API;

class Weapon extends API_Item {
    private $sub_type;
    private $damage_type;
    private $min_power;
    private $max_power;
    private $defense;
    private $infusion_slots;
    private $infix_upgrade;
    private $suffix_item_id;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        
        $this->sub_type = $API_item['weapon']['type'];
        $this->damage_type = $API_item['weapon']['damage_type'];
        $this->min_power = (int) $API_item['weapon']['min_power'];
        $this->max_power = (int) $API_item['weapon']['max_power'];
        $this->defense = (int) $API_item['weapon']['defense'];
        $this->infusion_slots = $API_item['weapon']['infusion_slots'];
        $this->infix_upgrade = $API_item['weapon']['infix_upgrade'];
        $this->suffix_item_id = $API_item['weapon']['suffix_item_id'];
    }
}