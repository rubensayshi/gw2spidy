<?php

namespace GW2Spidy\GW2API;

class ArmorV2 extends APIItemV2 {
    private $weight_class;
    private $defense;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['details']['type'];
        $this->weight_class = $APIItem['details']['weight_class'];
        $this->defense = $APIItem['details']['defense'];
        $this->infusion_slots = $APIItem['details']['infusion_slots'];
        $this->infix_upgrade = isset($APIItem['details']['infix_upgrade']) ? $APIItem['details']['infix_upgrade'] : array();
        $this->suffix_item_id = isset($APIItem['details']['suffix_item_id'])?$APIItem['details']['suffix_item_id']:null;;
    }
    
    public function getDefense() {
        return $this->defense;
    }
    
    public function getWeightClass() {
        return $this->weight_class;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-defense"><span>Defense:</span> {$this->getDefense()}</dd>
            {$this->getFormattedAttributes()}
            {$this->getFormattedSuffixItem()}
            <dd class="db-armorType">{$this->getSubType()}</dd>
            <dd class="db-armorWeight">{$this->getWeightClass()}</dd>
            <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
HTML;
        return $tooltip;
    }
}