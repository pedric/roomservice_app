/*
* Fredrik Larsson 840729-8218
* Gulpfile written by: Fredrik Larsson www.svartselet.se | fredrik@svartselet.se
*/

// Require modules
var gulp = require('gulp');
var concat = require('gulp-concat');
var jsmin = require('gulp-jsmin');
var cleanCss = require('gulp-clean-css');
var htmlmin = require('gulp-htmlmin');
var imagemin = require('gulp-imagemin');
var rename = require('gulp-rename');
var connect = require('gulp-connect');
var connectPHP = require('gulp-connect-php');
var livereload = require('gulp-livereload');
var browserSync = require('browser-sync');
var strip = require('gulp-strip-comments');

var reload  = browserSync.reload;

// Concat all js to app/js/main.js
gulp.task('concatJs', ['minifyJs'], function(){
	return gulp.src(['app/js/*.js', '!app/js/main.js'])
	.pipe(concat('main.js'))
	.pipe(gulp.dest('app/js'));
});

// Minify js and export to dist
gulp.task('minifyJs', function(){
	return gulp.src('app/js/main.js')
	.pipe(jsmin())
	.pipe(gulp.dest('dist/js'));
});

// Concat all css to app/css/main.css
gulp.task('concatCss', ['minifyCss'], function(){
	return gulp.src(['app/css/*.css', '!app/css/main.css'])
	.pipe(concat('main.css'))
	.pipe(gulp.dest('app/css'));
});

// Minify css and export to dist
gulp.task('minifyCss', function(){
	return gulp.src('app/css/main.css')
	.pipe(cleanCss())
	.pipe(gulp.dest('dist/css'));
});

// Minify and export html to dist (root)
gulp.task('htmlmin', function(){
	return gulp.src('app/*.html')
	.pipe(strip())
	.pipe(htmlmin({collapseWhitespace: true}))
	.pipe(gulp.dest('dist'));
});

// Export PHP to dist
gulp.task('exportPHP', ['includesPHP'], function(){
	return gulp.src('app/*.php')
	.pipe(gulp.dest('dist'));
});

// Export PHP (includes-folder) to dist
gulp.task('includesPHP', ['classesPHP'], function(){
	return gulp.src('app/includes/*.php')
	.pipe(gulp.dest('dist/includes'));
});

// Export PHP (classes-folder) to dist
gulp.task('classesPHP', function(){
	return gulp.src('app/classes/*.php')
	.pipe(gulp.dest('dist/classes'));
});

// Minify images and export to dist/images
gulp.task('imagemin', function(){
	return gulp.src('app/images/*')
	.pipe(imagemin())
	.pipe(gulp.dest('dist/images'));
});

// Export in-dev-sqlite DB:s
gulp.task('exportDB', function(){
	return gulp.src('app/db/*')
	.pipe(gulp.dest('dist/db'));
});

// Set up server and livereload (for PHP apps)
gulp.task('php', function() {
    connectPHP.server({ base: 'app', port: 8080, keepalive: true});
});

gulp.task('browser-sync',['php'], function() {
    browserSync({
        proxy: '127.0.0.1:8080',
        port: 8080,
        open: true,
        notify: false
    });
});

// Watch for changes of html, JS, CSS and images in /app
gulp.task('watch', function(){
	gulp.watch(['./app/css/*.css'], [reload]);
	gulp.watch(['./app/*.html'], [reload]);
	gulp.watch(['./app/js/*.js'], [reload]);
	gulp.watch(['./app/images/*'], [reload]);
	gulp.watch(['./app/*.php'], [reload]);
	gulp.watch(['./app/includes/*.php'], [reload]);
});

// Build app, run modules not evoked by chain
gulp.task( 'build', ['concatJs', 'concatCss', 'imagemin', 'htmlmin', 'watch', 'exportPHP', 'exportDB', 'browser-sync'] );

// Default -> Build app
gulp.task( 'default', ['build'] );