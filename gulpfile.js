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

// Concat all js to app/js/main.js
gulp.task('concatJs', ['minifyJs'], function(){
	return gulp.src(['app/js/*.js', '!app/js/main.js'])
	.pipe(concat('main.js'))
	.pipe(gulp.dest('app/js'))
	.pipe(connect.reload());
});

// Minify js and export to dist
gulp.task('minifyJs', function(){
	gulp.src('app/js/main.js')
	.pipe(jsmin())
	.pipe(gulp.dest('dist/js'));
});

// Concat all css to app/css/main.css
gulp.task('concatCss', ['minifyCss'], function(){
	return gulp.src(['app/css/*.css', '!app/css/main.css'])
	.pipe(concat('main.css'))
	.pipe(gulp.dest('app/css'))
	.pipe(connect.reload());
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
	.pipe(htmlmin({collapseWhitespace: true}))
	.pipe(gulp.dest('dist'))
	.pipe(connect.reload());
});

// Export PHP to dist
gulp.task('exportPHP', ['includesPHP'], function(){
	return gulp.src(['app/*.php'])
	.pipe(gulp.dest('dist'));
});

gulp.task('includesPHP', function(){
	return gulp.src(['app/includes/*.php'])
	.pipe(gulp.dest('dist/includes'))
	.pipe(connect.reload());
});

// Minify images and export to dist/images
gulp.task('imagemin', function(){
	return gulp.src('app/images/*')
	.pipe(imagemin())
	.pipe(gulp.dest('dist/images'))
	.pipe(connect.reload());
});

// Set up server and livereload
gulp.task('connect', function(){
	connect.server({
		root: 'app',
		livereload: true
	});
});

// Watch for changes of html, JS, CSS and images in /app
gulp.task('watch', function(){
	gulp.watch(['./app/css/*.css'], ['concatCss']);
	gulp.watch(['./app/*.html'], ['htmlmin']);
	gulp.watch(['./app/js/*.js'], ['concatJs']);
	gulp.watch(['./app/images/*'], ['imagemin']);
	gulp.watch(['./app/*.php', './app/includes/*.php'], ['exportPHP']);
});

// Build app, run modules not evoked by chain
gulp.task( 'build', ['concatJs', 'concatCss', 'imagemin', 'htmlmin', 'connect', 'watch', 'exportPHP'] );

// Default -> Build app
gulp.task( 'default', ['build'] );