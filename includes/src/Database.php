<?php

namespace MSD\src;

class Database extends \PDO
{
    function __construct()
    {
        $config = require_once(__DIR__ . '/../config.php');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=%s;',
            $config['mysql']['host'],
            $config['mysql']['port'],
            $config['mysql']['charset']
        );

        $user = $config['mysql']['username'];
        $pass = $config['mysql']['password'];

        parent::__construct($dsn, $user, $pass);
    }
}