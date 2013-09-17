<?php

namespace GW2Spidy\GW2API;

class Trinket extends APIItem {
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        $this->sub_type = $APIItem['trinket']['type'];
        $this->infusion_slots = $APIItem['trinket']['infusion_slots'];
        $this->infix_upgrade = (isset($APIItem['trinket']['infix_upgrade'])) ? $APIItem['trinket']['infix_upgrade'] : array();
        $this->suffix_item_id = $APIItem['trinket']['suffix_item_id'];
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            {$this->getFormattedAttributes()}
            {$this->getFormattedSuffixItem()}
            <dd class="db-damageType">{$this->getSubType()}</dd>
            <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
HTML;
        return $tooltip;
    }
}