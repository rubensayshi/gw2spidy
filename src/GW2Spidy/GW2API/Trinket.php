<?php

namespace GW2Spidy\GW2API;

class Trinket extends Equipment {
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        $this->sub_type = $API_Item['trinket']['type'];
        $this->infusion_slots = $API_Item['trinket']['infusion_slots'];
        $this->infix_upgrade = (isset($API_Item['trinket']['infix_upgrade'])) ? $API_Item['trinket']['infix_upgrade'] : array();
        $this->suffix_item_id = $API_Item['trinket']['suffix_item_id'];
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                {$this->getFormattedAttributes()}
                {$this->getFormattedSuffixItem()}
                <dd class="db-damageType">{$this->getSubType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}