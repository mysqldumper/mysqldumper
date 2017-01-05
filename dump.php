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
$do           = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if ($do) {
    switch ($do) {
        case 'dump':
            $fileName = filter_input(INPUT_GET, 'filename', FILTER_SANITIZE_STRING);
            $start    = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_NUMBER_INT);
            $end      = filter_input(INPUT_GET, 'end', FILTER_SANITIZE_NUMBER_INT);

            $msd->dumpDatabase($databaseName, $tableName, $fileName, $start, $end);
            break;

        case 'download':
            $fileName = filter_input(INPUT_GET, 'filename', FILTER_SANITIZE_STRING);

            if (! $fileName) {
                $msd->redirect('index');
            }

            $msd->downloadFile($fileName);
            break;

        default:
            $msd->redirect('index');
            break;
    }
} else {
    if (! $databaseName && ! $tableName) {
        $msd->redirect('index');
    }

    $msd->displayTemplate('dump_index');
}