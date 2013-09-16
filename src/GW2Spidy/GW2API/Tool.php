<?php

namespace GW2Spidy\GW2API;

class Tool extends APIItem {
    private $charges;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        $this->sub_type = $APIItem['tool']['type'];
        $this->charges = $APIItem['tool']['charges'];
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