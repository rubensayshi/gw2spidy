<?php

require dirname(__FILE__) . '/config/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

/*
 * bleh HTML directly in PHP, but fuck it for now ...
 */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.js"></script>
</head>
<body>
<div id="placeholder" style="width:600px;height:300px;"></div>

<script type="text/javascript">
$.ajax("/chart.php?id=4496", {
    success: function(data) {
        console.log(data);
        $.plot($("#placeholder"), data);
    }
});
</script>

</body>
</html>
