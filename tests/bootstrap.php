<?php
require dirname(__DIR__) . '/src/Net/EmptyPort.php';
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
$loader->add('dooaki\Test', __DIR__);

