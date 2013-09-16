<?php

namespace GW2Spidy\GW2API;

class Gizmo extends APIItem {
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['gizmo']['type'];
    }
}