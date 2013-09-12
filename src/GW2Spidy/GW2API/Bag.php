<?php

namespace GW2Spidy\GW2API;

class Bag extends API_Item {
    private $no_sell_or_sort;
    private $size;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->no_sell_or_sort = $API_Item['bag']['no_sell_or_sort'];
        $this->size = $API_Item['bag']['size'];
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