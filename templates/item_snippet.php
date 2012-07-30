<?php
/**
 * @var \GW2Spidy\DB\Item    $item
 * @var string               $tag        div or li
 * @var string               $href       link to (or null/empty if no link required)
 */
?>
<<?php echo $tag; ?> class="item">
    <?php if (isset($href) && $href): ?>
        <a href="<?php echo $href; ?>">
    <?php endif; ?>
    <div class="image">
        <img src="<?php echo $item->getImg(); ?>" />
    </div>
    <div class="stats">
        <div class="name"><?php echo $item->getName(); ?></div>
        <?php if ($item->getItemType()): ?>
            <div class="item-type"><?php echo $item->getItemType()->getTitle(); ?></div>
        <?php endif; ?>
        <?php if ($item->getItemSubType()): ?>
            <div class="item-sub-type"><?php echo $item->getItemSubType()->getTitle(); ?></div>
        <?php endif; ?>
        <div class="name"><?php echo $item->getName(); ?></div>
        <div class="rarity <?php echo $item->getRarityCSSClass(); ?>"><?php echo $item->getRarity(); ?></div>
        <?php if ($item->getRestrictionLevel()): ?>
            <div class="restriction-level">Required Level: <?php echo $item->getRestrictionLevel(); ?></div>
        <?php endif; ?>
    </div>
    <?php if (isset($href) && $href): ?>
        </a>
    <?php endif; ?>
</<?php echo $tag; ?>>