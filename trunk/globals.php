<?php
// change your base dir, and your timezone.
define('g_base_dir', '/var/www/orkut-os');
define('g_timezone', 'America/Sao_Paulo');

// orkut api keys. Get yours on: https://www.google.com/accounts/ManageDomains 
// more info: http://code.google.com/apis/accounts/docs/RegistrationForWebAppsAuto.html#new
define('g_orkut_consumer_key', '<<consumer key>>');
define('g_orkut_consumer_secret', '<<consumer secret>>');

// set this to determine the virtual directory where captcha.php is located. 
define('g_captcha_handler', '/orkut-os/captcha.php');

// set include path 
set_include_path(get_include_path() . PATH_SEPARATOR . g_base_dir.'/lib');

// Set the default timezone since many servers won't have this configured
date_default_timezone_set(g_timezone);

// Report everything, better to have stuff break here than in production
ini_set('error_reporting', E_ALL | E_STRICT);

// main requires
require_once 'orkut-3legged.php';
require_once g_base_dir.'/utils/error.php';

?>
