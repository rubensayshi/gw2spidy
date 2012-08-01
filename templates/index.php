<?php
/**
 * @var array[\GW2Spidy\DB\ItemType]    $types
 */

?>
<p>
Welcome to the Guild Wars 2 Spidy website! <br />
For discussion, questions, etc please goto <a href="http://www.guildwars2guru.com/topic/45667-guildwars2spidy-graphs-of-the-trade-market/">Guild Wars 2 Guru Forum</a>.
</p>
<?php foreach ($types as $type):
        if ($type->getTitle()):
?>
    <div class="well">
        <a href="/index.php?act=type&type=<?php echo $type->getId() ?>"><h4><?php echo $type->getTitle() ?></h4></a>
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
    </div>
<?php
        endif;
    endforeach; ?>