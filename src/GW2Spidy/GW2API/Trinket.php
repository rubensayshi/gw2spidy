<?php

namespace GW2Spidy\GW2API;

class Trinket extends API_Item {
    private $sub_type;
    private $infusion_slots;
    private $infix_upgrade;
    private $suffix_item_id;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        $this->sub_type = $API_item['trinket']['type'];
        $this->infusion_slots = $API_item['trinket']['infusion_slots'];
        $this->infix_upgrade = $API_item['trinket']['infix_upgrade'];
        $this->suffix_item_id = $API_item['trinket']['suffix_item_id'];
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getAttributes() {
        return $this->infix_upgrade['attributes'];
    }
    
    public function getBuff() {
        return $this->infix_upgrade['buff'];
    }
    
    public function getBuffDescription() {
        return nl2br($this->infix_upgrade['buff']['description'], false);
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
HTML;
        
        foreach ($this->getAttributes() as $attr) {
            $tooltip .= "\n<dd class=\"db-stat\">+{$attr['modifier']} {$attr['attribute']}</dd>";
        }
        
        $tooltip .= <<<HTML
                <dd class="db-stat">{$this->getBuffDescription()}</dd>
                <dd class="db-damageType">{$this->getSubType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}