<?php

# copy and rename to config.inc.php
# and fill in the values
# config.inc.php is on .gitignore so it won't be placed in version control!

define('LOGIN_EMAIL',    'test@example.com');
define('LOGIN_PASSWORD', 'p@ssw0rd');

define('AUTH_URL', 'https://account.guildwars2.com/login?redirect_uri=http://tradingpost-live.ncplatform.net/authenticate?source=%2F&game_code=gw2');

/*
 * 500 requests with a 5min timeout
 *  gives us 500 x (60 / (300 / 60)) = 6000 requests / hr
 */
define('SLOTS',        500);
define('SLOT_TIMEOUT', 300);