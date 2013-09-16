<?php

namespace GW2Spidy\GW2API;

class Tool extends API_Item {
    private $charges;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        $this->sub_type = $API_Item['tool']['type'];
        $this->charges = $API_Item['tool']['charges'];
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-consumableType">{$this->getSubType()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
HTML;
        return $tooltip;
    }
}