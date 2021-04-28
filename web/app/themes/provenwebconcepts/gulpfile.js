const gulp = require('gulp');
const sass = require('gulp-sass');
const watch = require('gulp-watch');
const sourcemaps = require('gulp-sourcemaps');
const cleancss = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const minify = require('gulp-minify');
const rename = require('gulp-rename');
const paths = {
    input: {
        sass: './assets/scss/**/*.scss',
        js: './assets/js/**/*.js',
        jsMin: './assets/js/**/*.min.js'
    },
    output: {
        css: './assets/css',
        js: './assets/js'
    }
};

gulp.task('default', ['sass', 'js']);
gulp.task('watch', ['sass:watch', 'js:watch']);

//region SASS
gulp.task('sass', () = >
gulp.src(paths.input.sass)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(autoprefixer())
    .pipe(cleancss({compatibility: 'ie9'}))
    .pipe(sourcemaps.write('./', {sourceRoot: './../scss'}))
    .pipe(gulp.dest(paths.output.css))
)
;

gulp.task('sass:watch', ['sass'], function () {
    watch(paths.input.sass, () = > gulp.start('sass')
)
    ;
});
//endregion

//region JS
gulp.task('js', () = >
gulp.src([paths.input.js, '!' + paths.input.jsMin])
    .pipe(minify({
        ext: {
            min: '.min.js'
        },
        noSource: true,
        ignoreFiles: ['.min.js']
    }))
    .pipe(gulp.dest(paths.output.js))
)
;

gulp.task('js:watch', ['js'], function () {
    watch([paths.input.js, '!' + paths.input.jsMin], () = > gulp.start('js')
)
    ;
});
//endregion