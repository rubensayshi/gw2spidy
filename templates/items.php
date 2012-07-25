<?php
/**
 * @var array[\GW2Spidy\DB\Item]    $items
 */

?>
<ul class="nav nav-pills nav-stacked">
    <?php foreach ($items as $item): ?>
        <li>
            <a href="/index.php?act=item&id=<?php echo $item->getDataId() ?>"><?php echo $item->getName() ?></a>
        </li>
    <?php endforeach; ?>
</ul>