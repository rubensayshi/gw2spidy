<?php

namespace GW2Spidy\GW2API;

class Bag extends APIItem {
    private $no_sell_or_sort;
    private $size;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->no_sell_or_sort = $APIItem['bag']['no_sell_or_sort'];
        $this->size = $APIItem['bag']['size'];
    }
}