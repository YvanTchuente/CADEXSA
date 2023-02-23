/* Front-End Workflow Configuation File */

// Initialize modules
const { src, dest, watch, series } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const postcss = require("gulp-postcss");
const uglify = require("gulp-uglify");
const autoprefixer = require("autoprefixer");
const cssnano = require("cssnano");

// File path variables
const sourceFiles = {
  sassPath: "resources/scss/**/*.scss",
  jsPath: "resources/js/**/*.js",
};

const destination = {
  cssPath: "public/css",
  jsPath: "public/js",
};

// Sass task
function buildCSS() {
  return src(sourceFiles.sassPath, { sourcemaps: true })
    .pipe(sass())
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(dest(destination.cssPath, { sourcemaps: "." }));
}

// Minifies the Main JS file for production environments
function minifyJs() {
  return src("public/js/main.js", { sourcemaps: true })
    .pipe(uglify())
    .pipe(dest(destination.jsPath, { sourcemaps: "." }));
}

// Watch task
function watchTask() {
  watch(sourceFiles.sassPath, series(buildCSS));
}

// Default Task
exports.default = series(buildCSS, watchTask);

// Minification of JS files
exports.minifyJs = minifyJs;
