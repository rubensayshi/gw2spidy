<?php

namespace GW2Spidy\GW2API;

class Gathering extends API_Item {
    private $sub_type;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        
        $this->sub_type = $API_item['gathering']['type'];
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-consumableType">{$this->getSubType()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}