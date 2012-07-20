<?php

require dirname(__FILE__) . '/config/config.inc.php';
require dirname(__FILE__) . '/autoload.php';

/*
 * bleh HTML directly in PHP, but fuck it for now ...
 */

if (isset($_GET['id']) && (string)(int)(string)$_GET['id'] === (string)$_GET['id']) {
    $id = (int)(string)$_GET['id'];
} else {
    $id = 4016;
}

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
$.ajax("/chart.php?id=<?php echo $id ?>", {
    success: function(chart) {
        chart = $.parseJSON(chart);

        $.each(chart, function(dataserie) {
            $.each(dataserie, function(entry) {
                var date = new Date(entry[0]);

                console.log("" + date + "");
            });
        });
        $.plot($("#placeholder"), chart, {
            xaxis: {
                mode: "time",
                timeformat: "%y-%0m-%0d %H:%S"
            }
        });
    }
});
</script>

</body>
</html>
