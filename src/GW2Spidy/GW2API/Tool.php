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