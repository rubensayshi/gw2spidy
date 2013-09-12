<?php

namespace GW2Spidy\GW2API;

class UpgradeComponent extends API_Item {
    private $sub_type;
    private $sub_flags;
    private $infusion_upgrade_flags;
    private $bonuses;
    private $infix_upgrade;
    private $suffix;
    
    public function __construct($API_item) {
        parent::__construct($API_item);
        
        $this->sub_type = $API_item['upgrade_component']['type'];
        $this->sub_flags = $API_item['upgrade_component']['flags'];
        $this->infusion_upgrade_flags = $API_item['upgrade_component']['infusion_upgrade_flags'];
        $this->bonuses = isset($API_item['upgrade_component']['bonuses']) ? $API_item['upgrade_component']['bonuses'] : array();
        $this->infix_upgrade = $API_item['upgrade_component']['infix_upgrade'];
        $this->suffix = $API_item['upgrade_component']['suffix'];
    }
    
    public function getSubType() {
        return $this->sub_type;
    }
    
    public function getAttributes() {
        return $this->infix_upgrade['attributes'];
    }
    
    public function getBuff() {
        return isset($this->infix_upgrade['buff']) ? $this->infix_upgrade['buff'] : null;
    }
    
    public function getBonuses() {
        $bonuses = "";
        
        for ($i = 1; $i <= count($this->bonuses); $i++) {
            $bonuses .= "($i) {$this->bonuses[$i - 1]}<br>";
        }
        
        return $bonuses;
    }
    
    public function getBuffDescription() {
        if (isset($this->infix_upgrade['buff']['description'])) {
            return nl2br($this->infix_upgrade['buff']['description'], false);
        }
        else {
            return $this->getBonuses();
        }
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
                
                <dd class="db-damageType">{$this->getSubType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-itemDescription">{$this->getBuffDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}