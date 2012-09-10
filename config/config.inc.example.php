<?php

# copy and rename to config.inc.php
# and fill in the values
# config.inc.php is on .gitignore so it won't be placed in version control!

define('LOGIN_EMAIL',    'test@example.com');
define('LOGIN_PASSWORD', 'p@ssw0rd');

// base URL for most things we do
define('TRADINGPOST_URL', 'https://tradingpost-live.ncplatform.net');
// base URL for most things we do
define('GEMEXCHANGE_URL', 'https://exchange-live.ncplatform.net');
// base URL for the login process
define('AUTH_URL', 'https://account.guildwars2.com');

// devmode, when on you get errors on your screen and twigcache will refresh when you edit files
// enable unless you're running in production!
define('DEV_MODE', true);

// sql dump mode, propel logs to configured log file when on
// define('SQL_LOG_MODE', true);

// when defined and TRUE we won't use memcache
// define('MEMCACHED_DISABLED', true);

// used to restrict access to csvs without much hassle (nor security :P)
$GLOBALS['csv_secrets'] = array(
    "c8f80801a691b82df99bb9880c602143", // md5(drakie)
);