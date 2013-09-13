<?php

namespace GW2Spidy\GW2API;

class Weapon extends Equipment {
    private $damage_type;
    private $min_power;
    private $max_power;
    private $defense;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        $this->sub_type = $API_Item['weapon']['type'];
        $this->damage_type = $API_Item['weapon']['damage_type'];
        $this->min_power = (int) $API_Item['weapon']['min_power'];
        $this->max_power = (int) $API_Item['weapon']['max_power'];
        $this->defense = (int) $API_Item['weapon']['defense'];
        $this->infusion_slots = $API_Item['weapon']['infusion_slots'];
        $this->infix_upgrade = isset($API_Item['weapon']['infix_upgrade']) ? $API_Item['weapon']['infix_upgrade'] : array();
        $this->suffix_item_id = $API_Item['weapon']['suffix_item_id'];
        
        $this->cleanAttributes();
    }
    
    public function getMinPower() {
        return $this->min_power;
    }
    
    public function getFormattedMinPower() {
        return number_format($this->min_power);
    }
    
    public function getMaxPower() {
        return $this->max_power;
    }
    
    public function getFormattedMaxPower() {
        return number_format($this->max_power);
    }
    
    public function getDamageType() {
        return $this->damage_type;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-weaponStrength"><span>Weapon Strength:</span> {$this->getFormattedMinPower()} - {$this->getFormattedMaxPower()}</dd>
                {$this->getFormattedAttributes()}
                {$this->getFormattedSuffixItem()}
                <dd class="db-weaponInfo">
                    <span class="db-weaponType">{$this->getSubType()}</span>
                    <span class="db-weaponRarity gwitem-{$this->getRarityLower()}">{$this->getRarity()}</span>
                </dd>
                <dd class="db-damageType">Damage Type: {$this->getDamageType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-itemDescription">{$this->getSoulboundStatus()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}