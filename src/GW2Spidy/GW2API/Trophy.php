<?php

namespace GW2Spidy\GW2API;

class Trophy extends API_Item {
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-damageType">{$this->getType()}</dd>
                <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}