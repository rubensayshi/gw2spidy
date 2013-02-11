<?php

use \DateTime;
use GW2Spidy\DB\RecipeQuery;

require dirname(__FILE__) . '/../autoload.php';

$offset = 20;
$i = 0;
$q = RecipeQuery::create();
$q->limit($offset)
  ->offset($i);

if (isset($argv[1])) {
    $q->filterByDataId($argv[1]);
}

while (($recipes = $q->find()) && $recipes->count()) {
	if($i % 10 == 0)
    	echo "$i\n";

    /* @var $recipe GW2Spidy\DB\Recipe */
    foreach ($recipes as $recipe) {
        $price = $recipe->calculatePrice();

        $recipe->setCost($price);
        $recipe->setSellPrice($recipe->getResultItem()->getMinSaleUnitPrice() * $recipe->getCount());
        $recipe->setProfit(($recipe->getSellPrice() * 0.85) - $price);

        $recipe->save();

        if (in_array('--dev', $argv)) {
            break;
        }
    }

    $q->offset(($i+=$offset));
    sleep(2);
}
