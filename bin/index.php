<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';

$config = new \src\config\Config(require BASE_PATH . '/config/autoload/commands.php');
$command = $config->get('command');

$application = new \Symfony\Component\Console\Application();
$application->add(new $command);
$application->run();
