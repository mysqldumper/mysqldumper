var dumpLock       = false;
var rowRecursion   = 0;
var tableRecursion = 0;

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
        setFilename();
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
            dumpDatabase(filename, databaseName, tableName, 10000);
        }
    });

});

setFilename = function()
{
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

    var filename = databaseName + '_' + ((tableName !== '') ? tableName + '_' : '') + filenameDate + '-' + filenameTime + '.sql';

    // Get default compression type
    var compressionType = $('#compressionType').find(':selected').val();

    if (compressionType === 'gzip') {
        filename = filename + '.gz';
    }

    $('#dumpFilename').text(filename);
}

dumpDatabase = function(filename, databaseName, tableName, rowLimit)
{
    rowLimit  = rowLimit  || 10000;
    tableName = tableName || '';

    rowRecursion++;

    if (tableName === '') {
        // Full database dump
        if (tablesObject[tableRecursion]) {
            staggerDump(databaseName, tablesObject[tableRecursion], filename, rowLimit, true);
        } else {
            $('#dumpingLoader').slideUp('fast');
            setProgressBar('Dump of `' + databaseName + '` completed.', true);
        }
    } else {
        // Table only dump
        staggerDump(databaseName, tableName, filename, rowLimit, false);
    }
}

setProgressBar = function(value, isText)
{
    isText = isText || false;

    if (isText) {
        $('#dumpProgress').text(value);
    } else {
        $('#dumpProgress').css('width', value + '%').text(value + '%');
    }
}

staggerDump = function(databaseName, tableName, filename, rowLimit, isFullDump)
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
                dumpDatabase(filename, databaseName, '', rowLimit);
            } else {
                tableRecursion++;
                rowRecursion = 0;
                dumpDatabase(filename, databaseName, '', rowLimit);
            }
        } else {
            setProgressBar(data);

            if (data == '100') {
                $('#dumpingLoader').slideUp('fast');
                setProgressBar('Dump of `' + databaseName + '`.`' + tableName + '` completed.', true);
            } else {
                dumpDatabase(filename, databaseName, tableName, rowLimit);
            }
        }
    });
}