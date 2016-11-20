'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var watch = require('gulp-watch');
var livereload = require('gulp-livereload');

gulp.task('sass', function () {
	return gulp.src('./web/assets/sass/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest('./web/assets/css'))
        .pipe(livereload());
});

gulp.task('stream', function (){
    livereload.listen();
    gulp.watch('./web/assets/sass/**/*.scss', ['sass']);
});
