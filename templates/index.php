<?php
/**
 * @var array[\GW2Spidy\DB\Item]    $items
 */

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.js"></script>
</head>
<body>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <a href="/index.php?act=item&id=<?php echo $item->getDataId() ?>">
                    <h3><?php echo $item->getName(); ?> <small><?php echo $item->getRestrictionLevel(); ?></small></h3>
                    <img src="<?php echo $item->getImg() ?>" />
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
