<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

// Init MySQLDumper.
$msd->init();

$databaseName = filter_input(INPUT_GET, 'database', FILTER_SANITIZE_STRING);

if (! $databaseName) {
    $msd->redirect('index');
}

// Get database information
$database = $msd->getDatabaseInformation($databaseName);

// Get table list.
$tables = $msd->getTablesInDatabase($databaseName);

$msd->displayTemplate('database_index');
