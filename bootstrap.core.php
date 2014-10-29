<?php
/*
|--------------------------------------------------------------------------
| PIMF bootstrap
|--------------------------------------------------------------------------
*/
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('BASE_PATH')) define('BASE_PATH', realpath(__DIR__) . DS);


// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    require_once 'autoload.core.php';
    require_once 'utils.php';
}
