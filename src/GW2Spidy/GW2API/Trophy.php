<?php

namespace GW2Spidy\GW2API;

class Trophy extends APIItem {
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-damageType">{$this->getType()}</dd>
            <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
HTML;
        return $tooltip;
    }
}