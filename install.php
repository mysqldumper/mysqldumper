<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

require_once('include/core.php');

if (isset($_POST['mysqlHost'])) {

    $data = filter_input_array(INPUT_POST, [
        'mysqlHost'     => FILTER_SANITIZE_STRING,
        'mysqlPort'     => FILTER_SANITIZE_NUMBER_INT,
        'mysqlUsername' => FILTER_SANITIZE_STRING,
        'mysqlPassword' => FILTER_UNSAFE_RAW
    ]);

    // Save to session
    $msd->tempSave('installVars', $data);

    // Check all inputs are present. Note that we shouldn't be checking if the password is present; on some MySQL installations,
    // the password is left empty.
    if (empty($data['mysqlHost']) || empty($data['mysqlPort']) || empty($data['mysqlUsername'])) {
        $error = 'Please ensure you have filled in the host, port and username parameters.';
        $msd->displayTemplate('install_msd');
        exit;
    }

    // Verify connection works.
    $verify = $msd->verifyConnection($data);

    if (! $verify) {
        $error = 'Could not connect to the database server. Please make sure your connection parameters are correct.';
        $msd->displayTemplate('install_msd');
        exit;
    }

    // Connection verified, let's proceed.
    $writeConfig = $msd->generateConfigFile($data);

    if (! $writeConfig) {
        $error = 'Could not write config file. Please create it manually.';
        $msd->displayTemplate('install_msd');
        exit;
    }

    $msd->tempClear('installVars');
    $msd->redirect('index');

} else {
    $msd->displayTemplate('install_msd');
}
