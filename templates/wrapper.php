<?php
/**
 * @var string    $content
 */
use GW2Spidy\Application;

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="http://people.iola.dk/olau/flot/jquery.flot.selection.js"></script>

    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-transition.js"></script>
    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-alert.js"></script>
    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-dropdown.js"></script>
    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script>
    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-tab.js"></script>
    <script src="http://twitter.github.com/bootstrap/assets/js/bootstrap-button.js"></script>

    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />

    <title>Guild Wars 2 Spidy</title>
    <style>
        html, body, .container-fluid, .content {
            height: 100%;
        }

        .container-fluid, .content {
            position: relative;
        }

        .wrapper {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -50px; /* same as the footer */
        }

        .push {
            height: 50px; /* same as the footer */
        }
        .footer-wrapper {
            position: relative;
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="/">Guild Wars 2 Spidy</a>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row-fluid wrapper">
            <div class="span1"></div>
            <div id="content" class="span10"><?php echo $content ?></div>
            <div class="span1"></div>
        </div>
        <div class="row-fluid push"></div>
        <div class="row-fluid footer-wrapper">
            <div class="span1"></div>
            <div class="span10">
                this is a <a href="https://github.com/rubensayshi/gw2spidy">open source project</a> by <a href="http://www.guildwars2guru.com/user/39936-drakie/">Drakie</a> / <a href="https://github.com/rubensayshi">rubensayshi</a>
                <div class="pull-right">
                    <small>rending page took <?php echo Application::getInstance()->getTime() ?></small>
                </div>
            </div>
            <div class="span1"></div>

        </div>
    </div>

    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-33623829-1']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
</body>
</html>
