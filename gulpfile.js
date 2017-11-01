// GENERAL deps
const gulp = require('gulp');
const inject = require('gulp-inject');
const rename = require('gulp-rename');
const source = require('vinyl-source-stream');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
const logger = require('gulp-logger');
const chalk = require('chalk');
const path = require('path');
const runSequence = require('run-sequence');
require('better-log/install');

// CSS deps
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const scssIncl = require('sass-include-paths');

// JS deps
const rollup = require('rollup-stream');
const buble = require('rollup-plugin-buble');
const uglify = require('gulp-uglify');
const nodeResolve = require('rollup-plugin-node-resolve')

// Config
const messages = require('./gulp.messages.js');
const themeRoot = 'wp-content/themes/theme';
const config = {
	srcPath: `${themeRoot}/src`,
	srcFiles: {
		js: `${themeRoot}/src/js/app.js`,
		css: `${themeRoot}/src/scss/style.scss`
	},
	distPath: `${themeRoot}/dist`,
	distFiles: {
		js: `${themeRoot}/dist/app.bundle.js`,
		css: `${themeRoot}/dist/style.bundle.css`
	},
	bundleExtNames: {
		js: {
			nomin: '.bundle.js',
			min: '.min.js' // because .bundle is already a part of filename
		},
		css: {
			nomin: '.bundle.css',
			min: '.min.css' // because .bundle is already a part of filename
		}
	},
	moduleRoot: `${themeRoot}/views/modules`,
	header: `${themeRoot}/header.php`,
	footer: `${themeRoot}/footer.php`,
	scssIncludePaths: [
		'node_modules',
		`${themeRoot}/src/scss`,
		`${themeRoot}/views/modules`
	]
}

class Time {
	constructor() {
		this.date = Date.now();
	}

	static getTime() {
		let time = new Date();
		let seconds = time.getSeconds() < 10 ? '0' + time.getSeconds() : time.getSeconds();
		let minutes = time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes();
		return `[${time.getHours()}:${minutes}:${seconds}]`;
	}

	start() {
		this.date = Date.now();
		return this.date;
	}

	end() {
		let result = Number(Date.now()) - Number(this.date);
		return result;
	}
}

const timer = new Time();

// CSS Bundling
gulp.task('compile:scss', () => {
	timer.start();
	return gulp.src(config.srcFiles.css)
		.pipe(plumber({
			errorHandler: function(error) {
				errorLog('compiling SCSS', error.message)
			}
		}))
		.pipe(sass({
			includePaths: config.scssIncludePaths
		}))
		.pipe(rename((path) => {
			path.extname = config.bundleExtNames.css.nomin
		}))
		.pipe(gulp.dest(config.distPath))
		.on('end', () => {
			logSuccess('SCSS compiled', timer.end());
		});
});

gulp.task('minify:css', () => {
	timer.start();
	return gulp.src(config.distFiles.css)
		.pipe(plumber({
			errorHandler: function(error) {
				errorLog('minifying SCSS', error.message)
			}
		}))
		.pipe(cssnano())
		.pipe(gulp.dest(config.distPath))
		.on('end', () => {
			logSuccess('CSS minified', timer.end());
		});
});

// JS Bundling
gulp.task('compile:js', () => {
	timer.start();
	return rollup({
			input: config.srcFiles.js,
			plugins: [buble(), nodeResolve()],
			format: 'iife'
		})
		.on('error', function(error) {
			errorLog('compiling JS', error)
		})
		.pipe(source('app.bundle.js'))
		.pipe(gulp.dest(config.distPath))
		.on('end', () => {
			logSuccess('JS compiled', timer.end());
		});
});

gulp.task('minify:js', () => {
	timer.start();
	return gulp.src(config.distFiles.js)
		.pipe(plumber({
			errorHandler: function(error) {
				errorLog('minifying JS', error.message)
			}
		}))
		.pipe(uglify())
		.pipe(gulp.dest(config.distPath))
		.on('end', () => {
			logSuccess('JS minified', timer.end());
		});
});

gulp.task('serve', ['compile:scss', 'compile:js'], () => {
	const watcherCSS = gulp.watch([config.srcPath + '/**/*.scss', config.moduleRoot + '/**/*.scss'], ['compile:scss']);
	watcherCSS.on('change', function(event) {
		messages.watch.change(event, ' Compiling SCSS... ');
	})

	const watcherJS = gulp.watch([config.srcPath + '/**/*.js', config.moduleRoot + '/**/*.js'], ['compile:js']);
	watcherJS.on('change', function(event) {
		messages.watch.change(event, ' Compiling JS... ');
	});

	messages.watch.init();
});

gulp.task('build', () => {
	messages.build.start();
	runSequence(
		'compile:scss',
		'minify:css',
		'compile:js',
		'minify:js',
		() => {
			messages.build.end();
		}
	);
});

function errorLog(errName, err) {
	messages.error.start(errName);
	console.log(err);
	messages.error.end();
	notify(`Error ${errName}. Check console for info.`);
}

function logSuccess(message, time) {
	messages.success(message, time);
}