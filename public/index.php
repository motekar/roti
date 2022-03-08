<?php

require '../vendor/autoload.php';

Motekar\Roti::getInstance()
    ->useCache(false)
    ->run('../example/routes');
