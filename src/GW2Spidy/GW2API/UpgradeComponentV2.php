<?php

namespace GW2Spidy\GW2API;

class UpgradeComponentV2 extends APIItemV2 {
    private $sub_flags;
    private $infusion_upgrade_flags;
    private $bonuses;
    private $suffix;
    
    public function __construct($APIItem) {
        parent::__construct($APIItem);
        
        $this->sub_type = $APIItem['details']['type'];
        $this->sub_flags = $APIItem['details']['flags'];
        $this->infusion_upgrade_flags = $APIItem['details']['infusion_upgrade_flags'];
        $this->bonuses = isset($APIItem['details']['bonuses']) ? $APIItem['details']['bonuses'] : array();
        $this->infix_upgrade = isset($APIItem['details']['infix_upgrade']) ? $APIItem['details']['infix_upgrade'] : array();
        $this->suffix = $APIItem['details']['suffix'];
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