<?php

namespace GW2Spidy\GW2API;

class Consumable extends API_Item {
    private $sub_type;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        
        $this->sub_type = $API_item['consumable']['type'];
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
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}