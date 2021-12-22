<?php

require '../vendor/autoload.php';

// require '../src/Roti.php';

ini_set('display_errors', 'On');

Motekar\Roti::getInstance()
    ->setMode('development')
    ->run('../test/routes');
