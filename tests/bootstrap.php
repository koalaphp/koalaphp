<?php
// timezone init
date_default_timezone_set('Asia/Shanghai');
define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath(dirname(__FILE__) . DS . '..' . DS));
define('ENVIRONMENT', 'develop');
define('UTF8_ENABLED', true);
define('APP_PATH', APP_ROOT . DS . 'tests' . DS . 'application');
define('CONFIG_PATH', APP_ROOT . DS . 'tests' . DS . 'config' . DS);

// autoload
require APP_ROOT . DS . 'vendor' . DS . 'autoload.php';

// 自定义的自动加载
require 'custom_autoload.php';



