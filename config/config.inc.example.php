<?php

# copy and rename to config.inc.php
# and fill in the values
# config.inc.php is on .gitignore so it won't be placed in version control!

define('LOGIN_EMAIL',    'test@example.com');
define('LOGIN_PASSWORD', 'p@ssw0rd');

define('AUTH_URL', 'https://account.guildwars2.com/login?redirect_uri=http://tradingpost-live.ncplatform.net/authenticate?source=%2F&game_code=gw2');

/*
 * 650 requests with a 5min timeout
 *  gives us 650 x (60 / (300 / 60)) = 7800 requests / hr = 2.166 requests / sec
 *
 * in 1 hour we create about 6500 jobs, our total requests / hr should exceed this by at least 10% to be able to catch up
 */
define('SLOTS',        650);
define('SLOT_TIMEOUT', 300);