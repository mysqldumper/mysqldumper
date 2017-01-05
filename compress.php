<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

// Init MySQLDumper.
$msd->init();

$fileName        = filter_input(INPUT_GET, 'filename', FILTER_SANITIZE_STRING);
$databaseName    = filter_input(INPUT_GET, 'database', FILTER_SANITIZE_STRING);
$tableName       = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING);
$compressionType = filter_input(INPUT_GET, 'compression', FILTER_SANITIZE_STRING);

if (! $fileName || ! $compressionType) {
    $msd->redirect('index');
}

$msd->compressDump($fileName, $databaseName, $tableName, $compressionType);