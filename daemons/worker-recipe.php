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
    echo  "[" . date("Y-m-d H:i:s") . "] $i\n";

    /* @var $recipe GW2Spidy\DB\Recipe */
    foreach ($recipes as $recipe) {
        if(is_object($recipe->getResultItem())) {
            $total = $recipe->calculatePrice();
            $price = $total['gold'];
            $karmaprice = $total['karma'];

            $recipe->setCost($price);
            $recipe->setKarmaCost($karmaprice);
            $recipe->setSellPrice($recipe->getResultItem()->getMinSaleUnitPrice() * $recipe->getCount());
            $recipe->setProfit(($recipe->getSellPrice() * 0.85) - $price);

            $recipe->save();

            if(isset($argv[1])) {
                echo "Name: {$recipe->getName()}, Count: {$recipe->getCount()}, Price: {$price}, Karmaprice: {$karmaprice}, Sell Price: {$recipe->getSellPrice()}, Profit: {$recipe->getProfit()}\n";
            }
        } else {
            echo "Error with recipe (No Item in database with ID \"{$recipe->getResultItemId()}\"): {$recipe->getName()}\n";
        }

        if (in_array('--dev', $argv)) {
            break;
        }
    }

    $q->offset(($i+=$offset));
    sleep(2);
}
