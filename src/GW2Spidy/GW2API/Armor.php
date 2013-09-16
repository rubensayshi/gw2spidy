<?php

namespace GW2Spidy\GW2API;

class Armor extends APIItem {
    private $weight_class;
    private $defense;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['armor']['type'];
        $this->weight_class = $APIItem['armor']['weight_class'];
        $this->defense = (int) $APIItem['armor']['defense'];
        $this->infusion_slots = $APIItem['armor']['infusion_slots'];
        $this->infix_upgrade = isset($APIItem['armor']['infix_upgrade']) ? $APIItem['armor']['infix_upgrade'] : array();
        $this->suffix_item_id = $APIItem['armor']['suffix_item_id'];
        
        $this->cleanAttributes();
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