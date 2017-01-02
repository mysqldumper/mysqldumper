var gulp        = require('gulp');
var sass        = require('gulp-sass');
var uglify      = require('gulp-uglify');
var concat      = require('gulp-concat');
var cleanCSS    = require('gulp-clean-css');
var argv        = require('yargs').argv;
var runSequence = require('run-sequence');

// All your Javascript files, in the order you want to concatenate them.
var jsFiles = [
    'node_modules/jquery/dist/jquery.js',
    'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
    'js/konami.js',
    'js/script.js'
];

// All your Sass files, in the order you want to compile them.
var scssFiles = [
    'sass/style.scss'
];

// Perform all style tasks.
gulp.task('styles', function() {
    if (argv.production) {
        console.log('Minifying compiled CSS files');

        gulp.src(scssFiles)
            .pipe(sass().on('error', sass.logError))
            .pipe(cleanCSS({debug: true, keepSpecialComments: 0}, function(details) {
                console.log('Minifying ' + details.name + ' reduced its size from ' + details.stats.originalSize + ' bytes to ' + details.stats.minifiedSize + ' bytes!');
            }))
            .pipe(gulp.dest('./assets/css/'));
    } else {
        gulp.src(scssFiles)
            .pipe(sass().on('error', sass.logError))
            .pipe(gulp.dest('./assets/css/'));
    }
});

// Perform all script tasks.
gulp.task('scripts', function() {
    if (argv.production) {
        console.log('Minifying compiled JS files');

        gulp.src(jsFiles)
            .pipe(concat('script.js'))
            .pipe(uglify())
            .pipe(gulp.dest('./assets/js/'));
    } else {
        gulp.src(jsFiles)
            .pipe(concat('script.js'))
            .pipe(gulp.dest('./assets/js/'));
    }
});

// Copy any necessary files to document root.
gulp.task('publish', function() {
    gulp.src('node_modules/font-awesome/fonts/**/*.{ttf,woff,eof,svg,woff2,otf}')
        .pipe(gulp.dest('assets/fonts'));
});

// This just lets us shortcut the gulp command to the sequence of styles -> scripts -> publish.
gulp.task('default', function() {
    runSequence('styles', 'scripts', 'publish');
});

// Watch task.
gulp.task('watch',function() {
    runSequence('default');
    gulp.watch(['sass/**/*.scss', 'js/**/*.js'],['default']);
});