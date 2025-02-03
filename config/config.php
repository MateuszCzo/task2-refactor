<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Core\Container\Container;

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

define('BASE_URL', $protocol . '://' . $host);

$container = new Container(__DIR__ . '/services/services.json');
