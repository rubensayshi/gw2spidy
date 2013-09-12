<?php

namespace GW2Spidy\GW2API;

class Back extends API_Item {
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        print_r($API_Item);
    }
    
    public function getSubType() {
        return null;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}