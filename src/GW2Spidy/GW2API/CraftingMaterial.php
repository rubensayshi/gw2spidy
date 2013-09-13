<?php

namespace GW2Spidy\GW2API;

class CraftingMaterial extends API_Item {    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
    }
    
    //TODO: Get crafting material ingredient-info to look like:
    //Crafting Ingredient: Huntsman(400), Artificer(400), Armorsmith(400), Leatherworker(400), Tailor(400)
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
                <dd class="db-ingredient-info">{$this->getType()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}