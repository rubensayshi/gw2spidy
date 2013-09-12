<?php

namespace GW2Spidy\GW2API;

class Armor extends API_Item {
    private $sub_type;
    private $weight_class;
    private $defense;
    private $infusion_slots;
    private $infix_upgrade;
    private $suffix_item_id;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->sub_type = $API_Item['armor']['type'];
        $this->weight_class = $API_Item['armor']['weight_class'];
        $this->defense = (int) $API_Item['armor']['defense'];
        $this->infusion_slots = $API_Item['armor']['infusion_slots'];
        $this->infix_upgrade = $API_Item['armor']['infix_upgrade'];
        $this->suffix_item_id = $API_Item['armor']['suffix_item_id'];
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getDefense() {
        return $this->defense;
    }
    
    public function getWeightClass() {
        return $this->weight_class;
    }
    
    public function getAttributes() {
        return $this->infix_upgrade['attributes'];
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-defense"><span>Defense:</span> {$this->getDefense()}</dd>
HTML;
        
        foreach ($this->getAttributes() as $attr) {
            $tooltip .= "\n<dd class=\"db-stat\">+{$attr['modifier']} {$attr['attribute']}</dd>";
        }
        
        $tooltip .= <<<HTML
                
                <dd class="db-armorType">{$this->getSubType()}</dd>
                <dd class="db-armorWeight">{$this->getWeightClass()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}