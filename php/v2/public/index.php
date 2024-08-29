<?php
error_reporting(E_ALL);

use Nw\WebService\App;

require __DIR__.'/../vendor/autoload.php';

$app = new App;
$app->start();
