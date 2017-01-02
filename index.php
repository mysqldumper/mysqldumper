<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

// Init MySQLDumper.
$msd->init();

// Get database list.
$databases = $msd->getDatabases();

$msd->displayTemplate('msd_index');
