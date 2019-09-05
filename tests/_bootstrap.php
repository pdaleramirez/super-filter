<?php

use craft\test\TestSetup;

ini_set('date.timezone', 'UTC');


error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', CRAFT_STORAGE_PATH.'/logs/phperrors.log');
ini_set('display_errors', 1);


// Use the current installation of Craft
define('CRAFT_STORAGE_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'storage');
define('CRAFT_TEMPLATES_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'templates');
define('CRAFT_CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'config');
define('CRAFT_MIGRATIONS_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'migrations');
define('CRAFT_TRANSLATIONS_PATH', __DIR__ . DIRECTORY_SEPARATOR . '_craft' . DIRECTORY_SEPARATOR . 'translations');
define('CRAFT_VENDOR_PATH', dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'vendor');

TestSetup::configureCraft();