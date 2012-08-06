<?php

# copy and rename to config.inc.php
# and fill in the values
# config.inc.php is on .gitignore so it won't be placed in version control!

define('LOGIN_EMAIL',    'test@example.com');
define('LOGIN_PASSWORD', 'p@ssw0rd');

define('AUTH_URL', 'https://account.guildwars2.com/login?redirect_uri=http://tradingpost-live.ncplatform.net/authenticate?source=%2F&game_code=gw2');

// define and set to true if you don't want to use memcache
// define('MEMCACHED_DISABLED', true);