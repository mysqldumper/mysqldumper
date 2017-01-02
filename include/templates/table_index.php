<?php

if (! defined('CORE_LOADED')) exit;
global $msd, $databaseName, $tableName, $tableInfo, $tableColumns, $tableData, $page;

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
                        <h3 class="panel-title">Table Information (`<?= $databaseName ?>`.`<?= $tableName ?>`)</h3>
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
                                <td>Row Count: <?= $tableInfo['rowcount'] ?></td>
                                <td>Est. Size: <?= $msd->byteConversion($tableInfo['size']) ?></td>
                            </tr>

                            <tr>
                                <td>Engine: <?= $tableInfo['engine'] ?></td>
                                <td>Collation: <?= $tableInfo['collation'] ?></td>
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
                        <h3 class="panel-title">Data in `<?= $databaseName ?>`.`<?= $tableName ?>`</h3>
                    </div>

                    <div class="table-container">
                        <table class="table table-hover aligned">
                            <thead>
                                <tr>
                                    <?php foreach ($tableColumns as $column) : ?>
                                        <th><?= $column ?></th>
                                    <?php endforeach; ?>
                                    
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($tableData) > 0) : ?>
                                    <?php foreach ($tableData as $row) : ?>
                                        <tr>
                                            <?php foreach ($tableColumns as $column) : ?>
                                                <td><?= $row[$column] ?></td>
                                            <?php endforeach; ?>

                                            <td>
                                                <i class="fa fa-pencil-square-o"></i>
                                            </td>
                                            <td>
                                                <i class="fa fa-trash"></i>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">No data found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($tableInfo['rowcount'] > 30) : ?>
                        <?php

                        // Pagination logic
                        $pageCount = ceil($tableInfo['rowcount'] / 30);
                        
                        // Page links
                        $startingPage = $page - 5;
                        $endingPage   = $page + 5;

                        if ($startingPage < 1) {
                            $startingPage = 1;
                        }

                        if ($endingPage > $pageCount) {
                            $endingPage = $pageCount;
                        }

                        // Previous link
                        if (($page - 1) < 1) {
                            $prevLink = '#';
                            $prevClass = 'disabled';
                        } else {
                            $prevLink = 'view_table.php?database=' . $databaseName . '&table=' . $tableName . '&page=' . ($page - 1);
                            $prevClass = '';
                        }

                        // Next link
                        if (($page + 1) > $pageCount) {
                            $nextLink = '#';
                            $nextClass = 'disabled';
                        } else {
                            $nextLink = 'view_table.php?database=' . $databaseName . '&table=' . $tableName . '&page=' . ($page + 1);
                            $nextClass = '';
                        }

                        ?>
                        <div class="table-pagination">
                            <nav aria-label="Table Pagination">
                                <ul class="pagination">
                                    <li class="<?= $prevClass ?>">
                                        <a href="<?= $prevLink ?>" aria-label="Previous">
                                            <span aria-hidden="true">
                                                <i class="fa fa-angle-left"></i>
                                            </span>
                                        </a>
                                    </li>
                                    
                                    

                                    <?php for ($i = $startingPage; $i <= $endingPage; $i++) : ?>
                                        <li class="<?= (($i === $page) ? 'active' : '') ?>"><a href="view_table.php?database=<?= $databaseName ?>&table=<?= $tableName ?>&page=<?= $i ?>"><?= $i ?><?= (($i === $page) ? ' <span class="sr-only">(current)</span>' : '') ?></a></li>
                                    <?php endfor; ?>

                                    <li class="<?= $nextClass ?>">
                                        <a href="<?= $nextLink ?>" aria-label="Next">
                                            <span aria-hidden="true">
                                                <i class="fa fa-angle-right"></i>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>