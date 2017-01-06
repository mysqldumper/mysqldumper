<?php

if (! defined('CORE_LOADED')) exit;
global $msd;

?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <p class="text-muted">MySQLDumper by MSD Team</p>
                <p class="text-muted">v<?= $msd->version ?></p>
            </div>
        </div>
    </div>
</footer>

<?php if ($msd->isInstalled()) : ?>
    <div class="settings-pane" id="settingsPane">
        <button class="open-settings-pane" id="openSettingsPane">
            <i class="fa fa-cogs fa-fw fa-inverse" id="openSettingsPaneIcon"></i>
            <i class="fa fa-times fa-fw fa-inverse" id="closeSettingsPaneIcon"></i>
        </button>
        
        <div class="settings-pane-inner">
            <div class="row">
                <div class="col-sm-12 settings-pane-title">Settings</div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-settings form-control" id="changeDumpPath" data-toggle="modal" data-target="#changeDumpPathModal">Set Dump Path</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changeDumpPathModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Set Dump Path</h4>
                    </div>

                    <div class="modal-body">
                        <input type="text" class="form-control" id="settingDumpPath" value="<?= $msd->settings->dumpSettings->path ?>">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save-setting" data-modal-id="changeDumpPathModal" data-key="dumpPath" data-input-id="settingDumpPath">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>