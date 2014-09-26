<?php

namespace GW2Spidy\GW2API;

class CraftingMaterialV2 extends APIItemV2 {
    public function __construct($APIItem) {
        parent::__construct($APIItem);
    }
    
    //TODO: Get crafting material ingredient-info to look like:
    //Crafting Ingredient: Huntsman(400), Artificer(400), Armorsmith(400), Leatherworker(400), Tailor(400)
    public function getTooltipDescription() {
        $tooltip = <<<HTML
            <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
            <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            <dd class="db-ingredient-info">{$this->getType()}</dd>
HTML;
        return $tooltip;
    }
}