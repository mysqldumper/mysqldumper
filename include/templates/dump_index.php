<?php

if (! defined('CORE_LOADED')) exit;
global $msd, $databaseName, $tableName;

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

        <div class="row tables-row">
            <div class="col-sm-12">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php if ($tableName) : ?>
                            <input type="hidden" name="databaseName" id="databaseName" value="<?= $databaseName ?>">
                            <input type="hidden" name="tableName" id="tableName" value="<?= $tableName ?>">
                            <h3 class="panel-title">Dump `<?= $databaseName ?>`.`<?= $tableName ?>`</h3>
                        <?php else : ?>
                            <input type="hidden" name="databaseName" id="databaseName" value="<?= $databaseName ?>">
                            <h3 class="panel-title">Dump `<?= $databaseName ?>`</h3>
                        <?php endif; ?>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <?php if ($tableName) : ?>
                                    <th>Total Rows</th>
                                <?php else : ?>
                                    <th>Total Tables</th>
                                <?php endif; ?>

                                <th>Est. Size (before compression)</th>
                                <th>Compression</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php

                            if ($tableName) {
                                $progressMax = $msd->getTotalRows($databaseName, $tableName);
                            } else {
                                $progressMax = $msd->getTotalTables($databaseName);
                            }

                            ?>
                            <tr>
                                <td id="dumpFilename"></td>
                                <td><?= number_format($progressMax) ?></td>
                                <td>
                                    <?= $msd->getEstimateSize($databaseName, $tableName) ?>
                                </td>
                                <td>
                                    <select class="form-control compressionType" name="compressionType" id="compressionType">
                                        <option value="off" selected>Off</option>
                                        <!-- <option value="gzip" selected>On (gzip)</option> -->
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="panel-body">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" id="dumpProgress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?= $progressMax ?>">
                            </div>
                        </div>

                        <button class="btn btn-default form-control" id="dumpThatDatabase">Dump!</button>

                        <div class="row" id="dumpingLoader">
                            <div class="col-sm-12">
                                <img src="assets/images/loader.gif" alt="Dumping in progress">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php if (! $tableName) : ?>
    <script type="text/javascript">
        var tablesObject = <?= $msd->getJsonTables($databaseName) ?>;
    </script>
<?php endif; ?>