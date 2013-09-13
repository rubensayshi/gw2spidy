<?php

namespace GW2Spidy\GW2API;

class Consumable extends API_Item {
    private $duration_ms;
    private $sub_description;
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->sub_type = $API_Item['consumable']['type'];
        $this->duration_ms = $API_Item['consumable']['duration_ms'];
        $this->sub_description = $API_Item['consumable']['description'];
    }
    
    private function getNourishment() {
        if ($this->getSubType() == "Food") {
            $input = $this->duration_ms;

            $uSec = $input % 1000;
            $input = floor($input / 1000);

            $seconds = $input % 60;
            $input = floor($input / 60);

            $minutes = $input % 60;
            $input = floor($input / 60);
            
            $hours = $input % 60;
            $input = floor($input / 60);
            
            $time = array();
            
            if ($hours > 0) {
                $time[] = "$hours h";
            }
            
            if ($minutes > 0) {
                $time[] = "$minutes m";
            }
            
            if ($seconds > 0) {
                $time[] = "$seconds s";
            }
            
            $time_string = implode(',', $time);
            
            $nourishment = "Nourishment({$time_string}): {$this->sub_description}";
            
            return $nourishment;
        }
        
        return null;
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-itemDescription">{$this->getNourishment()}</dd>
                <dd class="db-consumableType">{$this->getSubType()}</dd>
                <dd class="db-requiredLevel">Required Level: {$this->getLevel()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}