<?php
session_start();
define('ROOT', dirname(__FILE__));
require ROOT. '/components/Autoload.php';
require ROOT. '/components/Db.php';

$routing = new Router();
$routing->start();
