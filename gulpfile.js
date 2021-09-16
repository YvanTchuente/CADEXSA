/* Front-End Workflow Configuation File */

// Initialize modules
const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const browsersync = require('browser-sync').create();

// File path variables
const sourceFiles = {
    htmlPath: "app/**/*.html",
    sassPath: "app/static/scss/**/*.scss",
    jsPath: "app/static/js/**/*.js"
}

const destination = {
    cssPath: "app/static/dist/css",
    jsPath: "app/static/dist/js"
}

// Sass task
function buildCSS() {
    return src(sourceFiles.sassPath, { sourcemaps: true })
        .pipe(sass())
        .pipe(postcss([ autoprefixer(), cssnano() ]))
        .pipe(dest(destination.cssPath, { sourcemaps: '.' }));
}

// Concatenates JS files into one JS Main file
function JsConcatTask() {
    return src(sourceFiles.jsPath, { sourcemaps: true })
        .pipe(concat('main.js'))
        .pipe(dest(destination.jsPath, { sourcemaps: "." }));
}

// Minifies the Main JS file for production environments
function minifyJs() {
    return src("app/static/dist/js/main.js", { sourcemaps: true })
        .pipe(uglify())
        .pipe(dest(destination.jsPath, { sourcemaps: "." }));
}

// Browser synchronization task
function browsersyncServe(done) {
    browsersync.init({
        server: {
            baseDir: './app'
        }
    });
    done();
}

// Reload browser Task
function browsersyncReload(done) {
    browsersync.reload();
    done();
}

// Watch task
function watchTask() {
    watch(sourceFiles.htmlPath,browsersyncReload);
    watch(sourceFiles.sassPath,series(buildCSS, browsersyncReload));
    watch(sourceFiles.jsPath,series(JsConcatTask, browsersyncReload));
}

// Default Task
exports.default = series(
    buildCSS, 
    JsConcatTask,
    browsersyncServe,
    watchTask
);

// Minification of JS files
exports.minifyJs = minifyJs;