var dumpLock                    = false;
var rowRecursion                = 0;
var tableRecursion              = 0;
var fixedFilename               = '';
var fixedFilenameWithExtension  = '';

$(document).ready(function () {

    var ee_fired = false;

    var easter_egg = new Konami(function() {
        if (! ee_fired) {
            ee_fired = true;
            $('footer .container .row .col-sm-12').append("<p class=\"text-muted italic\">Easter Egg! Shout out to Plum, TeOz, c0r3, Protag, CK, AK, Paiman, DedSec & TeamPS</p>");
            $('footer .container .row .col-sm-12').append("<p class=\"text-muted italic\">\"What is dead may never die\"</p>");

            $('footer .container .row .col-sm-12').append("<iframe src=\"https://www.youtube.com/embed/Sagg08DrO5U?autoplay=1\" style=\"border: none;\"></iframe>");
        }
    });

    $('.dropDatabase').click(function (e) {
        var confirmWithUser = confirm("Are you sure you want to drop the database `" + $(this).data('database-name') + "`?");

        if (! confirmWithUser) {
            e.preventDefault();
            return false;
        }

        return true;
    });

    $('.dropTable').click(function (e) {
        var confirmWithUser = confirm("Are you sure you want to drop the table `" + $(this).data('database-name') + "`.`" + $(this).data('table-name') + "`?");

        if (! confirmWithUser) {
            e.preventDefault();
            return false;
        }

        return true;
    });

    if($('#dumpFilename').length) {
        // Dump functions onload
        setFilename(true);
    }

    $('#compressionType').change(function(e) {
        setFilename();
    });

    $('#dumpThatDatabase').click(function(e) {
        if (! dumpLock) {
            dumpLock = true;
            $('#dumpThatDatabase').prop('disabled', true);

            // Alright, we're firing the dump process. Lock everything in and let 'er rip!
            var compressionType = $('#compressionType').find(':selected').val();
            var filename        = $('#dumpFilename').text();
            var databaseName    = $('#databaseName').val();
            var tableName       = (($('#tableName').length) ? $('#tableName').val() : '');
            var rowsOrTables    = $('#dumpProgress').prop('aria-valuemax');

            $('#compressionType').parent().text($('#compressionType').find(':selected').text());

            $('#dumpThatDatabase').slideUp('fast');
            $('#dumpingLoader').slideDown('fast');

            setProgressBar('0');

            // Do the dump!
            dumpDatabase(filename, databaseName, tableName, 10000, compressionType);
        }
    });

    $('#downloadThatDatabase').click(function(e) {
        downloadFile();
    });

    $('#openSettingsPane').click(function(e) {
        if ($('#settingsPane').hasClass('open')) {
            settingsPane('close');
        } else {
            settingsPane('open')
        }
    });

    $('.settings-pane-mask').click(function(e) {
        settingsPane('close');
    });

    $('.btn-save-setting').click(function(e) {
        var $this = $(this);
        var theId = $this.data('input-id');
        var key   = $this.data('key');
        var value = $('#' + theId).val();

        saveSetting(key, value, $this);
    });

});

settingsPane = function(action)
{
    action = action || 'open';

    if (action == 'open') {
        // Open form
        $('.settings-pane-mask').fadeIn();

        $('#openSettingsPaneIcon').fadeOut(function() {
            $('#closeSettingsPaneIcon').fadeIn();
        });

        $('#settingsPane').animate({
            right: '0px'
        }, 400);

        $('body').animate({
            marginRight: '300px'
        }, 400);

        $('#settingsPane').addClass('open');
    } else if (action == 'close') {
        // Close form
        $('.settings-pane-mask').fadeOut();

        $('#closeSettingsPaneIcon').fadeOut(function() {
            $('#openSettingsPaneIcon').fadeIn();
        });

        $('#settingsPane').animate({
            right: '-300px'
        }, 400);

        $('body').animate({
            marginRight: '0'
        }, 400);

        $('#settingsPane').removeClass('open');
    }

    $('body').toggleClass('settings-pane-open');
}

saveSetting = function(key, value, $this)
{
    $this.prop('disabled', true);
    $this.text('Saving..');

    $.post('save_setting.php', {
        key   : key,
        value : value
    }, function(data) {
        if (data == 'saved') {
            $this.prop('disabled', false);
            $this.text('Save');
            $('#' + $this.data('modal-id')).modal('hide');
        } else {
            alert('Your settings were not saved due to an error.');
            $this.prop('disabled', false);
            $this.text('Save');
        }
    });
}

setFilename = function(setGlobally)
{
    setGlobally = setGlobally || false;

    var databaseName = $('#databaseName').val();
    var tableName    = '';

    if ($('#tableName').length) {
        tableName = $('#tableName').val();
    }

    // Date
    var d            = new Date();
    var month        = d.getMonth() + 1;
    var day          = d.getDate();
    var hours        = ((('' + d.getHours()).length == 1) ? '0' + d.getHours() : d.getHours());
    var minutes      = ((('' + d.getMinutes()).length == 1) ? '0' + d.getMinutes() : d.getMinutes());
    var seconds      = ((('' + d.getSeconds()).length == 1) ? '0' + d.getSeconds() : d.getSeconds());
    var filenameDate = d.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') + month + '-' + (('' + day).length < 2 ? '0' : '') + day;
    var filenameTime = hours + '-' + minutes + '-' + seconds;

    if (setGlobally) {
        fixedFilename = databaseName + '_' + ((tableName !== '') ? tableName + '_' : '') + filenameDate + '-' + filenameTime;
    }

    // Get default compression type
    var compressionType = $('#compressionType').find(':selected').val();

    filename = fixedFilename + $('#compressionType').find(':selected').data('extension');

    $('#dumpFilename').text(filename);

    fixedFilenameWithExtension = filename;
}

dumpDatabase = function(filename, databaseName, tableName, rowLimit, compressionType)
{
    rowLimit        = rowLimit  || 10000;
    tableName       = tableName || '';
    compressionType = compressionType || 'off';

    rowRecursion++;

    if (tableName === '') {
        // Full database dump
        if (tablesObject[tableRecursion]) {
            staggerDump(databaseName, tablesObject[tableRecursion], filename, rowLimit, true, compressionType);
        } else {
            $('#dumpingLoader').slideUp('fast');
            setProgressBar('100');

            if (compressionType != 'off') {
                setProgressBar('Compressing dump..');
                compressDump(filename, databaseName, tableName, compressionType, true);
            } else {
                setProgressBar('Dump of `' + databaseName + '` completed.', true);
            }
        }
    } else {
        // Table only dump
        staggerDump(databaseName, tableName, filename, rowLimit, false, compressionType);
    }
}

setProgressBar = function(value, isText)
{
    isText = isText || false;

    if (value == '100') {
        showDownloadButton();
    }

    if (isText) {
        $('#dumpProgress').text(value);
    } else {
        if (value == '100') {
            $('#dumpProgress').removeClass('active').removeClass('progress-bar-striped').addClass('progress-bar-success');
        }

        $('#dumpProgress').css('width', value + '%').text(value + '%');
    }
}

staggerDump = function(databaseName, tableName, filename, rowLimit, isFullDump, compressionType)
{
    $.get('dump.php', {
        do:       'dump',
        database: databaseName,
        table:    tableName,
        filename: filename,
        start:    rowLimit * (rowRecursion - 1),
        end:      rowLimit * rowRecursion
    }, function(data) {
        if (isFullDump) {
            var tableCount   = tablesObject.length;
            var tablesDumped = tableRecursion;

            var percent        = (tablesDumped / tableCount) * 100;
            var percentRounded = Math.round(percent);

            setProgressBar(percentRounded.toString());

            if (data != '100') {
                dumpDatabase(filename, databaseName, '', rowLimit, compressionType);
            } else {
                tableRecursion++;
                rowRecursion = 0;
                dumpDatabase(filename, databaseName, '', rowLimit, compressionType);
            }
        } else {
            setProgressBar(data);

            if (data == '100') {
                $('#dumpingLoader').slideUp('fast');
                
                if (compressionType != 'off') {
                    setProgressBar('Compressing dump..', true);
                    compressDump(filename, databaseName, tableName, compressionType, false);
                } else {
                    setProgressBar('Dump of `' + databaseName + '`.`' + tableName + '` completed.', true);
                }
            } else {
                dumpDatabase(filename, databaseName, tableName, rowLimit, compressionType);
            }
        }
    });
}

compressDump = function(filename, databaseName, tableName, compressionType, isFullDump)
{
    $.get('compress.php', {
        filename:    filename,
        database:    databaseName,
        table:       tableName,
        compression: compressionType
    }, function(data) {
        if (data == '100') {
            if (isFullDump) {
                setProgressBar('Dump of `' + databaseName + '` completed.', true);
            } else {
                setProgressBar('Dump of `' + databaseName + '`.`' + tableName + '` completed.', true);
            }
        }
    });
}

showDownloadButton = function()
{
    var filename = fixedFilenameWithExtension;

    $('#downloadThatDatabase').text('Download ' + filename);
    $('#downloadThatDatabase').slideDown('fast');
}

downloadFile = function()
{
    window.location = 'dump.php?do=download&filename=' + fixedFilenameWithExtension;
}