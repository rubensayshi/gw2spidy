<?php

namespace GW2Spidy\GW2API;

class BagV2 extends APIItemV2 {
    private $no_sell_or_sort;
    private $size;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->no_sell_or_sort = $APIItem['details']['no_sell_or_sort'];
        $this->size = $APIItem['details']['size'];
    }
}