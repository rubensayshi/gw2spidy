<?php
/**
 * @var array[\GW2Spidy\DB\ItemType]    $types
 */

?>
<?php foreach ($types as $type):
        if ($type->getTitle()):
?>
    <div class="well">
        <a href="/index.php?act=type&type=<?php echo $type->getId() ?>"><h4><?php echo $type->getTitle() ?></h4></a>
        <?php if ($type->getSubTypes()->count()) : ?>
            <ul class="nav nav-pills nav-stacked">
                <?php foreach ($type->getSubTypes() as $subtype):
                        if ($subtype->getTitle()):
                ?>
                    <li>
                        <a href="/index.php?act=type&type=<?php echo $type->getId() ?>&subtype=<?php echo $subtype->getId() ?>"><?php echo $subtype->getTitle() ?></a>
                    </li>
                <?php
                        endif;
                    endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php
        endif;
    endforeach; ?>