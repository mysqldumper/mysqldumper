<?php

if (! defined('CORE_LOADED')) exit;
global $msd, $tables, $databaseName, $database;

?>
<section class="page-content">
    <div class="container">
        <div class="row logo-row">
            <div class="col-sm-12">
                <a href="index.php">
                    <img src="assets/images/MSD-Logo.png" alt="MySQLDumper" class="img-responsive">
                </a>
            </div>
        </div>

        <div class="row database-info-row">
            <div class="col-sm-12">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Database Information (`<?= $databaseName ?>`)</h3>
                    </div>

                    <table class="table dbinfo-table">
                        <tbody>
                            <tr>
                                <?php

                                $size = 0;

                                foreach ($tables as $table) {
                                    $size = intval($size + $table['size']);
                                }

                                ?>
                                <td>Table Count: <?= count($tables) ?></td>
                                <td>Est. Size: <?= $msd->byteConversion($size) ?></td>
                            </tr>

                            <tr>
                                <td>Charset: <?= $database['charset'] ?></td>
                                <td>Collation: <?= $database['collation'] ?></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

        <div class="row tables-row">
            <div class="col-sm-12">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tables</h3>
                    </div>

                    <table class="table table-hover aligned">
                        <thead>
                            <tr>
                                <th>Table Name</th>
                                <th>Row Count</th>
                                <th>Est. Size</th>
                                <th>Dump</th>
                                <th>Drop</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($tables) > 0) : ?>
                                <?php foreach ($tables as $table) : ?>
                                    <tr>
                                        <td>
                                            <a href="view_table.php?database=<?= $databaseName ?>&table=<?= $table['name'] ?>">
                                                <?= $table['name'] ?>
                                            </a>
                                        </td>

                                        <td><?= $table['rows'] ?></td>
                                        <td><?= $msd->byteConversion($table['size']) ?></td>
                                        
                                        <td>
                                            <a href="dump.php?database=<?= $databaseName ?>&table=<?= $table['name'] ?>">
                                                <i class="fa fa-cloud-download"></i>
                                            </a>
                                        </td>
                                        
                                        <td>
                                            <a href="drop_table.php?database=<?= $databaseName ?>&table=<?= $table['name'] ?>" class="dropTable" data-database-name="<?= $databaseName ?>" data-table-name="<?= $table['name'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">No tables found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>