<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

// Init MySQLDumper.
$msd->init();

$databaseName = filter_input(INPUT_GET, 'database', FILTER_SANITIZE_STRING);
$tableName    = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING);
$page         = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

if (! $databaseName || ! $tableName) {
    $msd->redirect('index');
}

if (! $page || $page < 1) {
    $page = 1;
}

$page = intval($page);

// Get table information.
$tableInfo = $msd->getTableInformation($databaseName, $tableName);

// Get table columns.
$tableColumns = $msd->getTableColumns($databaseName, $tableName);

// Get table data.
$tableData = $msd->getTableData($databaseName, $tableName, $page);

// Display template
$msd->displayTemplate('table_index');
