<?php

namespace GW2Spidy\GW2API;

class ContainerV2 extends APIItemV2 {
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['details']['type'];
    }
}