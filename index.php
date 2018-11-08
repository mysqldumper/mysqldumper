<?php

// Require the autoloader
require_once(__DIR__ . '/vendor/autoload.php');

// Init the core (and load in everything else)
require_once(__DIR__ . '/includes/Core.php');

$core = MSD\Core::run();
