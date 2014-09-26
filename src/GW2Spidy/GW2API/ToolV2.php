<?php

namespace GW2Spidy\GW2API;

class ToolV2 extends APIItemV2 {
    private $charges;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        $this->sub_type = $APIItem['details']['type'];
        $this->charges = $APIItem['details']['charges'];
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