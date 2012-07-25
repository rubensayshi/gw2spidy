<?php
/**
 * @var array[\GW2Spidy\DB\ItemType]    $types
 */

?>
<ul class="nav nav-pills nav-stacked">
    <?php foreach ($types as $type): ?>
        <li>
            <a href="/index.php?act=type&type=<?php echo $type->getId() ?>"><?php echo $type->getTitle() ?></a>
        </li>
        <?php foreach ($type->getSubTypes() as $subtype): ?>
            <li>
                <a href="/index.php?act=type&type=<?php echo $type->getId() ?>&subtype=<?php echo $subtype->getId() ?>"><?php echo $subtype->getTitle() ?></a>
            </li>
        <?php endforeach; ?>
    <?php endforeach; ?>
</ul>