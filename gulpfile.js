'use strict'

var gulp = require('gulp');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var sassConfig = {
    precision: 6,
    indentedSyntax: false,
    sourceComments: false,
    outputStyle: 'compressed'
}

// Watching SCSS files
gulp.task('sass:watch', function () {
  gulp.watch('./resources/assets/sass/**/*.scss', ['sass']);
});

gulp.task('default', ['sass:watch']);

//var checkCSS = require( 'gulp-check-unused-css' );
var purify = require( 'purify-css' );
gulp.task('check', function () {
    var content = ['./public/*.js','./public/html/*.html'];
    var css = ['./public/css/*.css'];

    var options = {
        // Will write purified CSS to this file.
        output: './public/style.min.css',
        // Will minify CSS code in addition to purify.
        minify: true,
        //Logs out removed selectors.
        //rejected: true
    };
    purify(content, css, options);
});
gulp.task('get-css', function () {
    var folder_process = './public/_process_html_css/';
    var content = [folder_process+'*.html'];
    var css = [folder_process+'*.css'];

    var options = {
        // Will write purified CSS to this file.
        output: folder_process+'style.min.css',
        // Will minify CSS code in addition to purify.
        minify: true,
        //Logs out removed selectors.
        //rejected: true
    };
    purify(content, css, options);
});
gulp.task('get-css-controller', function () {
    var folder_process = './app/Http/Controllers/FrontEndSiteEdu/views/';
    var content = [
        folder_process+'**/*.php',
        './resources/views/site-edu/**/*.php',
        './app/Http/Models/*.php',
        './public/js/tag-input/*.js',
        './public/js/slick/*.js',
        //'./public/**/*.js',
    ];
    var css = ['./public/css/edu.min.css'];

    var options = {
        // Will write purified CSS to this file.
        output: './public/css/main.min.css',
        // Will minify CSS code in addition to purify.
        minify: true,
        //Logs out removed selectors.
        //rejected: true
    };
    purify(content, css, options);
});
gulp.task('site-edu', function () {
    gulp.src('./resources/assets/sass/edu.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('edu.css'))
        .pipe(sass(sassConfig))
        .pipe(concat('edu.min.css'))
        .pipe(gulp.dest('./public/css'));

    var content = ['./public/*.js','./public/html/*.html'];
    var css = ['./public/css/*.css'];

    var options = {
        // Will write purified CSS to this file.
        output: './public/css/edu.min.css',
        // Will minify CSS code in addition to purify.
        minify: false,//đổi thành true
        // Logs out removed selectors.
        //rejected: true
    };
    purify(content, css, options);
});
gulp.task('site-coin', function () {
    gulp.src('./resources/assets/sass/app.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('coin.css'))
        .pipe(sass(sassConfig))
        .pipe(concat('coin.min.css'))
        .pipe(gulp.dest('./public/css'));

    var content = ['./public/*.js','./public/html/*.html'];
    var css = ['./public/css/*.css'];

    var options = {
        // Will write purified CSS to this file.
        output: './public/css/coin.min.css',
        // Will minify CSS code in addition to purify.
        minify: false,
        // Logs out removed selectors.
        //rejected: true
    };
    purify(content, css, options);
});