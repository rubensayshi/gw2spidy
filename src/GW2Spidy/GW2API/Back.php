<?php

namespace GW2Spidy\GW2API;

class Back extends APIItem {
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->infusion_slots = $APIItem['back']['infusion_slots'];
        $this->infix_upgrade = isset($APIItem['back']['infix_upgrade']) ? $APIItem['back']['infix_upgrade'] : array();
        $this->suffix_item_id = $APIItem['back']['suffix_item_id'];
        
        $this->cleanAttributes();
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            {$this->getFormattedAttributes()}
            {$this->getFormattedSuffixItem()}
            <dd class="db-armorType">{$this->getSubType()}</dd>
            <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
HTML;
        return $tooltip;
    }
}