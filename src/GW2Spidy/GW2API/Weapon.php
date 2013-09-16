<?php

namespace GW2Spidy\GW2API;

class Weapon extends APIItem {
    private $damage_type;
    private $min_power;
    private $max_power;
    private $defense;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        $this->sub_type = $APIItem['weapon']['type'];
        $this->damage_type = $APIItem['weapon']['damage_type'];
        $this->min_power = (int) $APIItem['weapon']['min_power'];
        $this->max_power = (int) $APIItem['weapon']['max_power'];
        $this->defense = (int) $APIItem['weapon']['defense'];
        $this->infusion_slots = $APIItem['weapon']['infusion_slots'];
        $this->infix_upgrade = isset($APIItem['weapon']['infix_upgrade']) ? $APIItem['weapon']['infix_upgrade'] : array();
        $this->suffix_item_id = $APIItem['weapon']['suffix_item_id'];
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
HTML;
        return $tooltip;
    }
}