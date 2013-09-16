<?php

namespace GW2Spidy\GW2API;

class Gathering extends API_Item {
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->sub_type = $API_Item['gathering']['type'];
    }
}