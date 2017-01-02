<?php

if (! defined('CORE_LOADED')) exit;
global $error;

?>
<section class="page-content">
    <div class="container">
        <div class="row logo-row">
            <div class="col-sm-12">
                <img src="assets/images/MSD-Logo.png" alt="MySQLDumper" class="img-responsive">
            </div>
        </div>

        <div class="row setup-row">
            <div class="col-sm-6 col-sm-offset-3">

                <?php if (! empty($error)) : ?>
                    <div class="row error-row">
                        <div class="col-sm-12 error-message">
                            <?= $error ?>
                        </div>
                    </div>
                <?php endif; ?>
                    
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Install MySQLDumper</h3>
                    </div>
                    <div class="panel-body">
                        <form action="install.php" method="post" class="form-horizontal">

                            <div class="form-group">
                                <label for="mysqlHost" class="col-sm-2 control-label">Host</label>
                                <div class="col-sm-10">
                                    <input type="text" name="mysqlHost" class="form-control" id="mysqlHost" placeholder="Host" value="<?= ((! empty($_SESSION['installVars']['mysqlHost'])) ? $_SESSION['installVars']['mysqlHost'] : 'localhost') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mysqlPort" class="col-sm-2 control-label">Port</label>
                                <div class="col-sm-10">
                                    <input type="number" name="mysqlPort" min="0" max="999999999" class="form-control" id="mysqlPort" placeholder="Port" value="<?= ((! empty($_SESSION['installVars']['mysqlPort'])) ? $_SESSION['installVars']['mysqlPort'] : '3306') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mysqlUsername" class="col-sm-2 control-label">Username</label>
                                <div class="col-sm-10">
                                    <input type="text" name="mysqlUsername" class="form-control" id="mysqlUsername" placeholder="Username" value="<?= ((! empty($_SESSION['installVars']['mysqlUsername'])) ? $_SESSION['installVars']['mysqlUsername'] : '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mysqlPassword" class="col-sm-2 control-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="text" name="mysqlPassword" class="form-control" id="mysqlPassword" placeholder="Password" value="<?= ((! empty($_SESSION['installVars']['mysqlPassword'])) ? $_SESSION['installVars']['mysqlPassword'] : '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <button type="submit" class="btn btn-default form-control">Install</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>