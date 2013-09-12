<?php

namespace GW2Spidy\GW2API;

class Weapon extends API_Item {
    private $sub_type;
    private $damage_type;
    private $min_power;
    private $max_power;
    private $defense;
    private $infusion_slots;
    private $infix_upgrade;
    private $suffix_item_id;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        
        $this->sub_type = $API_item['weapon']['type'];
        $this->damage_type = $API_item['weapon']['damage_type'];
        $this->min_power = (int) $API_item['weapon']['min_power'];
        $this->max_power = (int) $API_item['weapon']['max_power'];
        $this->defense = (int) $API_item['weapon']['defense'];
        $this->infusion_slots = $API_item['weapon']['infusion_slots'];
        $this->infix_upgrade = isset($API_item['weapon']['infix_upgrade']) ? $API_item['weapon']['infix_upgrade'] : array();
        $this->suffix_item_id = $API_item['weapon']['suffix_item_id'];
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getMinPower() {
        return $this->min_power;
    }
    
    public function getMaxPower() {
        return $this->max_power;
    }
    
    public function getDamageType() {
        return $this->damage_type;
    }
    
    public function getAttributes() {
        return isset($this->infix_upgrade['attributes']) ? $this->infix_upgrade['attributes'] : array();
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-weaponStrength"><span>Weapon Strength:</span> {$this->getMinPower()} - {$this->getMaxPower()}</dd>
HTML;
        
        foreach ($this->getAttributes() as $attr) {
            $tooltip .= "\n<dd class=\"db-stat\">+{$attr['modifier']} {$attr['attribute']}</dd>";
        }
        
        $tooltip .= <<<HTML
                
                <dd class="db-weaponInfo">
                    <span class="db-weaponType">{$this->getSubType()}</span>
                    <span class="db-weaponRarity gwitem-{$this->getRarityLower()}">{$this->getRarity()}</span>
                </dd>
                <dd class="db-damageType">Damage Type: {$this->getDamageType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}