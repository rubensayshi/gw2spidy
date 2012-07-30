<?php

use GW2Spidy\Application;

/**
 * @var string                      $baseurl
 * @var int                         $page
 * @var int                         $lastpage
 * @var array[\GW2Spidy\DB\Item]    $items
 */
?>
<ul class="nav nav-pills nav-stacked">
    <?php foreach ($items as $item): ?>
        <?php echo Application::getInstance()->render("item_snippet", array('item' => $item, 'tag' => 'li', 'href' => "/index.php?act=item&id={$item->getDataId()}")); ?>
    <?php endforeach; ?>
</ul>
<div class="pagination">
    <ul>
        <li class="<?php if ($page <= 1): ?>disabled<?php endif; ?>"><a href="<?php echo "{$baseurl}&page=" . $page -1 ?>">Prev</a></li>
        <?php for ($i = 1; $i < $lastpage; $i++): ?>
            <li class="<?php if ($i == $page): ?>active<?php endif; ?>">
                <a href="<?php echo "{$baseurl}&page={$i}" ?>"><?php echo $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="<?php if ($page >= $lastpage): ?>disabled<?php endif; ?>"><a href="<?php echo "{$baseurl}&page=" . $page +1 ?>">Next</a></li>
    </ul>
</div>