<?php

namespace GW2Spidy\GW2API;

class UpgradeComponent extends API_Item {
    private $sub_flags;
    private $infusion_upgrade_flags;
    private $bonuses;
    private $suffix;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->sub_type = $API_Item['upgrade_component']['type'];
        $this->sub_flags = $API_Item['upgrade_component']['flags'];
        $this->infusion_upgrade_flags = $API_Item['upgrade_component']['infusion_upgrade_flags'];
        $this->bonuses = isset($API_Item['upgrade_component']['bonuses']) ? $API_Item['upgrade_component']['bonuses'] : array();
        $this->infix_upgrade = isset($API_Item['upgrade_component']['infix_upgrade']) ? $API_Item['upgrade_component']['infix_upgrade'] : array();
        $this->suffix = $API_Item['upgrade_component']['suffix'];
        
        $this->cleanAttributes();
        
        //Some items have buff descriptions which are just added into the items attributes automatically in game
        if (isset($this->infix_upgrade['buff']['description'])) {
            $this->addBuffsToAttributes();
        }
    }
    
    public function getBonuses() {
        $bonuses = "";
        
        for ($i = 1; $i <= count($this->bonuses); $i++) {
            $bonuses .= "($i) {$this->bonuses[$i - 1]}<br>";
        }
        
        return $bonuses;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                {$this->getFormattedAttributes()}
                <dd class="db-slotted-item">{$this->getBonuses()}</dd>
                <dd class="db-damageType">{$this->getSubType()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}