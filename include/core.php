<?php

/**
 * MySQLDumper v2.2
 * @author JB <jb@p0wersurge.com>
 */

@ini_set("memory_limit","9999M");
@ini_set("max_input_time", 0);
@ini_set("max_execution_time", 0);

define('CORE_LOADED', true);

session_start();

class MSD
{
    public $db;
    public $version = '2.2.0-dev';

    function __construct()
    {
        // No construct method just yet. Maybe I'll fill this method with magic some day.
    }

    public function init()
    {
        if (! $this->isInstalled()) {
            $msd->redirect('install');
        }

        // include(__DIR__ . '/config.php');

        // $this->db = new mysqli($config['mysqlHost'], $config['mysqlUsername'], $config['mysqlPassword'], '', $config['mysqlPort']);
    }

    public function displayTemplate($template)
    {
        $html = '';

        $html .= $this->head_main();
        $html .= $this->header();
        
        $html .= $this->getTemplate($template);

        $html .= $this->footer();
        $html .= $this->foot_main();
        
        echo $html;
    }

    private function getTemplate($template)
    {
        @include(__DIR__ . '/templates/' . $template . '.php');
    }

    private function head_main()
    {
        $template = 'head_main';
        $getTemplate = $this->getTemplate($template);
    }

    private function header()
    {
        $template = 'header';
        $getTemplate = $this->getTemplate($template);
    }

    private function footer()
    {
        $template = 'footer';
        $getTemplate = $this->getTemplate($template);
    }

    private function foot_main()
    {
        $template = 'foot_main';
        $getTemplate = $this->getTemplate($template);
    }

    public function verifyConnection(array $connectionVars)
    {
        $mysqli = new mysqli($connectionVars['mysqlHost'], $connectionVars['mysqlUsername'], $connectionVars['mysqlPassword'], '', $connectionVars['mysqlPort']);

        if ($mysqli->connect_error) {
            return false;
        }

        $this->db = $mysqli;

        return true;
    }

    public function generateConfigFile(array $connectionVars)
    {
        $getConfig = @file_get_contents(__DIR__ . '/config-example.php'); 

        $getConfig = preg_replace('/<--DB_HOST-->/', $connectionVars['mysqlHost'], $getConfig);
        $getConfig = preg_replace('/<--DB_PORT-->/', $connectionVars['mysqlPort'], $getConfig);
        $getConfig = preg_replace('/<--DB_USER-->/', $connectionVars['mysqlUsername'], $getConfig);
        $getConfig = preg_replace('/<--DB_PASS-->/', $connectionVars['mysqlPassword'], $getConfig);

        $write = @file_put_contents(__DIR__ . '/config.php', $getConfig);

        if ($write) {
            return true;
        } else {
            return false;
        }
    }

    public function tempSave($index, $data)
    {
        $_SESSION[$index] = $data;
    }

    public function tempClear($index)
    {
        unset($_SESSION[$index]);
    }

    public function redirect($to)
    {
        header('Location: ' . $to . '.php');
        exit;
    }

    public function isInstalled()
    {
        if (! file_exists(__DIR__ . '/config.php')) {
            return false;
        }

        require_once(__DIR__ . '/config.php');

        if (! $this->verifyConnection($config)) {
            return false;
        }

        return true;
    }

    public function getTableCount($databaseName)
    {
        $sql = "SELECT COUNT(*) FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ?";
        $prepped = $this->db->prepare($sql);
        $prepped->bind_param('s', $databaseName);

        $prepped->execute();

        $prepped->bind_result($count);

        while ($prepped->fetch()) {
            $return = $count;
        }

        return $return;
    }

    public function getEstimateSize($databaseName, $tableName = false)
    {
        if (! $tableName) {
            $sql = "SELECT SUM(`DATA_LENGTH` + `INDEX_LENGTH`) AS `size` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ?";
            $prepped = $this->db->prepare($sql);
            $prepped->bind_param('s', $databaseName);

            $prepped->execute();

            $prepped->bind_result($size);

            while ($prepped->fetch()) {
                $return = $size;
            }
        } else {
            $return = 0;

            $sql = "SELECT SUM(`DATA_LENGTH` + `INDEX_LENGTH`) AS `size` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?";
            $prepped = $this->db->prepare($sql);
            $prepped->bind_param('ss', $databaseName, $tableName);

            $prepped->execute();

            $prepped->bind_result($size);

            while ($prepped->fetch()) {
                $return = $return + $size;
            }
        }

        return $this->byteConversion($return);
    }

    public function byteConversion($bytes, $precision = 2)
    {
        $bytes = intval($bytes);

        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';
        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';
        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';
        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    public function getDatabases()
    {
        $databases = [];
        $query = $this->db->query("SELECT `SCHEMA_NAME` FROM `information_schema`.`SCHEMATA`");

        while ($database = $query->fetch_array()) {
            $databases[] = $database['SCHEMA_NAME'];
        }
        
        return $databases;
    }

    public function getTablesInDatabase($databaseName)
    {
        $tables = [];

        $prepped = $this->db->prepare("SELECT `TABLE_NAME`, (`DATA_LENGTH` + `INDEX_LENGTH`) AS `size`, `TABLE_ROWS` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ?");

        $prepped->bind_param('s', $databaseName);

        $prepped->execute();

        $prepped->bind_result($name, $size, $rowCount);

        while ($prepped->fetch()) {
            $tables[] = [
                'name' => $name,
                'size' => $size,
                'rows' => $rowCount
            ];
        }
        
        return $tables;
    }

    public function getDatabaseInformation($databaseName)
    {
        $info = [];

        $prepped = $this->db->prepare("SELECT `DEFAULT_CHARACTER_SET_NAME` AS `charset`, `DEFAULT_COLLATION_NAME` AS `collation` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` = ?");

        $prepped->bind_param('s', $databaseName);

        $prepped->execute();

        $prepped->bind_result($charset, $collation);

        while ($prepped->fetch()) {
            $info = [
                'charset'   => $charset,
                'collation' => $collation
            ];
        }
        
        return $info;
    }

    public function getTableInformation($databaseName, $tableName)
    {
        $info = [];

        $prepped = $this->db->prepare("SELECT `ENGINE` AS `engine`, `TABLE_COLLATION` AS `collation`, `TABLE_ROWS` AS `rowcount`, (`DATA_LENGTH` + `INDEX_LENGTH`) AS `size` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?");

        $prepped->bind_param('ss', $databaseName, $tableName);

        $prepped->execute();

        $prepped->bind_result($engine, $collation, $rowCount, $size);

        while ($prepped->fetch()) {
            $info = [
                'engine'    => $engine,
                'collation' => $collation,
                'rowcount'  => $rowCount,
                'size'      => $size
            ];
        }
        
        return $info;
    }

    public function getTableColumns($databaseName, $tableName)
    {
        $columns = [];

        $prepped = $this->db->prepare("SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ? ORDER BY `ORDINAL_POSITION` ASC");

        $prepped->bind_param('ss', $databaseName, $tableName);

        $prepped->execute();

        $prepped->bind_result($columnName);

        while ($prepped->fetch()) {
            $columns[] = $columnName;
        }
        
        return $columns;
    }

    public function getTableData($databaseName, $tableName, $page = 1)
    {
        $data = [];

        // First let's do our offset calculations.
        $limit  = 30;
        $offset = ($page - 1) * $limit;

        // Grab the necessary data.
        $query = $this->db->query("SELECT * FROM `" . $databaseName . "`.`" . $tableName . "` LIMIT " . $offset . "," . $limit);

        // Loop the data.
        while ($row = $query->fetch_array()) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function getTableDumpData($databaseName, $tableName, $start, $end)
    {
        $data = [];

        // Grab the necessary data.
        $query = $this->db->query("SELECT * FROM `" . $databaseName . "`.`" . $tableName . "` LIMIT " . $start . "," . $end);

        // Loop the data.
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
        }
        
        return $data;
    }

    public function dropDatabase($databaseName)
    {
        $this->db->query("DROP DATABASE `" . $databaseName . "`");
    }

    public function dropTable($databaseName, $tableName)
    {
        $this->db->query("DROP TABLE `" . $databaseName . "`.`" . $tableName . "`");
    }

    public function getTotalRows($databaseName, $tableName)
    {
        $prepped = $this->db->prepare("SELECT `TABLE_ROWS` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?");

        $prepped->bind_param('ss', $databaseName, $tableName);

        $prepped->execute();

        $prepped->bind_result($count);

        while ($prepped->fetch()) {
            $return = $count;
        }

        return $return;
    }

    public function getTotalTables($databaseName)
    {
        $prepped = $this->db->prepare("SELECT COUNT(*) AS `count` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ?");

        $prepped->bind_param('s', $databaseName);

        $prepped->execute();

        $prepped->bind_result($count);

        while ($prepped->fetch()) {
            $return = $count;
        }

        return $return;
    }

    public function getCreateTableSyntax($databaseName, $tableName)
    {
        $prepped = $this->db->prepare("SHOW CREATE TABLE `" . $databaseName . "`.`" . $tableName . "`");

        $prepped->execute();

        $prepped->bind_result($table, $syntax);

        while ($prepped->fetch()) {
            $return = $syntax . ';';
        }

        return $return;
    }

    public function getInsertRowSyntax($databaseName, $tableName, $tableData, $perLine = 500)
    {
        $insertString = '';
        $columns      = $this->getTableColumns($databaseName, $tableName);
        $implodedCols = implode('`,`', $columns);

        for ($i = 0; $i < (10000 / $perLine); $i++) {
            if (! empty($tableData)) {
                $insertString .= "INSERT INTO `" . $tableName . "` (`" . $implodedCols . "`) VALUES ";

                for ($x = 0; $x < $perLine; $x++) {
                    if (! empty($tableData[$x])) {

                        if ($x > 0) {
                            $insertString .= ", ";
                        }

                        foreach ($tableData[$x] as $k => $data) {
                            $tableData[$x][$k] = $this->db->real_escape_string($data);
                        }

                        $insertString .= "('";

                        $insertString .= implode("','", $tableData[$x]);
                        unset($tableData[$x]);

                        $insertString .= "')";
                    }
                }

                sort($tableData);

                $insertString .= ";" . PHP_EOL;
            }
        }

        return $insertString;
    }

    private function getPercentComplete($databaseName, $tableName, $end)
    {
        $totalRows = $this->getTotalRows($databaseName, $tableName);

        if ($totalRows == 0) {
            $totalRows = 1;
        }

        if ($end > $totalRows) {
            $end = $totalRows;
        }

        $percent = ($end / $totalRows) * 100;

        return round($percent);
    }

    public function getJsonTables($databaseName)
    {
        $tables = [];

        $prepped = $this->db->prepare("SELECT `TABLE_NAME` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ?");

        $prepped->bind_param('s', $databaseName);

        $prepped->execute();

        $prepped->bind_result($name);

        while ($prepped->fetch()) {
            $tables[] = $name;
        }
        
        return json_encode($tables);
    }

    public function dumpDatabase($databaseName, $tableName, $fileName, $start, $end)
    {
        $pathToDumpsFolder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps';
        $pathToDumpFile    = $pathToDumpsFolder . DIRECTORY_SEPARATOR . $fileName;

        // Create the file if it doesn't exist.
        if (! file_exists($pathToDumpFile)) {
            file_put_contents($pathToDumpFile, '-- MySQLDumper v' . $this->version . PHP_EOL);
            file_put_contents($pathToDumpFile, '-- Database: ' . $databaseName . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '-- Date:     ' . date('Y-m-d H:i') . PHP_EOL, FILE_APPEND);

            // Who ya gonna call?
            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--                       __---__' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--                    _-       _--______' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--               __--( /     \ )XXXXXXXXXXXXX_' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--             --XXX(   O   O  )XXXXXXXXXXXXXXX-' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--            /XXX(       U     )        XXXXXXX\\' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--          /XXXXX(              )--_  XXXXXXXXXXX\\' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--         /XXXXX/ (      O     )   XXXXXX   \XXXXX\\' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--         XXXXX/   /            XXXXXX   \__ \XXXXX----' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--         XXXXXX__/          XXXXXX         \__----  -' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '-- ---___  XXX__/          XXXXXX      \__         ---' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--   --  --__/   ___/\  XXXXXX            /  ___---=' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--     -_    ___/    XXXXXX              \'--- XXXXXX' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--       --\/XXX\ XXXXXX                      /XXXXX' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--         \XXXXXXXXX                        /XXXXX/' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--          \XXXXXX                        _/XXXXX/' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--            \XXXXX--__/              __-- XXXX/' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--             --XXXXXXX---------------  XXXXX--' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--                \XXXXXXXXXXXXXXXXXXXXXXXX-' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--                  --XXXXXXXXXXXXXXXXXX-' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '--            * * * * * who ya gonna call? * * * * *' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);
        }

        // Write the table header, and dump the CREATE TABLE syntax.
        if ($start == 0) {
            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '-- Dumping structure for `' . $tableName . '`' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);

            file_put_contents($pathToDumpFile, $this->getCreateTableSyntax($databaseName, $tableName) . PHP_EOL, FILE_APPEND);

            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, '-- Dumping data for `' . $tableName . '`' . PHP_EOL, FILE_APPEND);
            file_put_contents($pathToDumpFile, PHP_EOL, FILE_APPEND);
        }
        
        $tableData = $this->getTableDumpData($databaseName, $tableName, $start, $end);

        $insertSyntax = $this->getInsertRowSyntax($databaseName, $tableName, $tableData, 500);

        file_put_contents($pathToDumpFile, $insertSyntax, FILE_APPEND);

        echo $this->getPercentComplete($databaseName, $tableName, $end);
    }
}

$msd = new MSD();