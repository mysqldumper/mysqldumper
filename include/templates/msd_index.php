<?php

if (! defined('CORE_LOADED')) exit;
global $msd, $databases;

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

        <div class="row databases-row">
            <div class="col-sm-12">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Databases</h3>
                    </div>

                    <table class="table table-hover aligned">
                        <thead>
                            <tr>
                                <th>Database Name</th>
                                <th>Tables</th>
                                <th>Est. Size</th>
                                <th>Dump</th>
                                <th>Drop</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($databases) > 0) : ?>
                                <?php foreach ($databases as $database) : ?>
                                    <tr>
                                        <td>
                                            <a href="view_database.php?database=<?= $database ?>">
                                                <?= $database ?>
                                            </a>
                                        </td>

                                        <td><?= $msd->getTableCount($database) ?></td>
                                        <td><?= $msd->getEstimateSize($database) ?></td>
                                        
                                        <td>
                                            <a href="dump.php?database=<?= $database ?>">
                                                <i class="fa fa-cloud-download"></i>
                                            </a>
                                        </td>
                                        
                                        <td>
                                            <a href="drop_database.php?database=<?= $database ?>" class="dropDatabase" data-database-name="<?= $database ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">No databases found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</section>