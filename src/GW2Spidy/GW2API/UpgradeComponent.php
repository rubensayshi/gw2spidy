<?php

namespace GW2Spidy\GW2API;

class UpgradeComponent extends APIItem {
    private $sub_flags;
    private $infusion_upgrade_flags;
    private $bonuses;
    private $suffix;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['upgrade_component']['type'];
        $this->sub_flags = $APIItem['upgrade_component']['flags'];
        $this->infusion_upgrade_flags = $APIItem['upgrade_component']['infusion_upgrade_flags'];
        $this->bonuses = isset($APIItem['upgrade_component']['bonuses']) ? $APIItem['upgrade_component']['bonuses'] : array();
        $this->infix_upgrade = isset($APIItem['upgrade_component']['infix_upgrade']) ? $APIItem['upgrade_component']['infix_upgrade'] : array();
        $this->suffix = $APIItem['upgrade_component']['suffix'];
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
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            {$this->getFormattedAttributes()}
            <dd class="db-slotted-item">{$this->getBonuses()}</dd>
            <dd class="db-damageType">{$this->getSubType()}</dd>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
HTML;
        return $tooltip;
    }
}