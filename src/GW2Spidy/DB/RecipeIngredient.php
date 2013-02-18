<?php

namespace GW2Spidy\DB;

use GW2Spidy\DB\om\BaseRecipeIngredient;


/**
 * Skeleton subclass for representing a row from the 'recipe_ingredient' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.gw2spidy
 */
class RecipeIngredient extends BaseRecipeIngredient {
    protected $okOnImport = false;

    public function getOkOnImport() {
        return $this->okOnImport;
    }

    public function setOkOnImport($okOnImport = true) {
        $this->okOnImport = $okOnImport;
    }

} // RecipeIngredient
