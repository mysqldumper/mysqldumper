<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

// Init MySQLDumper.
$msd->init();

$key   = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_STRING);
$value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);

if (! $key || ! $value) {
    echo 'failed';
    exit;
}

$msd->saveSetting($key, $value);