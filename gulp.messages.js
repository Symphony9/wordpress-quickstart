const chalk = require('chalk');
const path = require('path');
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

module.exports = {
	build: {
		start: () => {
			console.log('');
			console.log(chalk.bgBlue(' ======================= '));
			console.log(chalk.bgBlue(' <<<< BUILD STARTED >>>> '));
			console.log(chalk.bgBlue(' ======================= '));
			console.log('');
		},
		end: () => {
			console.log('');
			console.log(chalk.bgBlue(' ======================== '));
			console.log(chalk.bgBlue(' <<<< BUILD COMPLETE >>>> '));
			console.log(chalk.bgBlue(' ======================== '));
			console.log('');
		}
	},
	error: {
		start: (errName) => {
			console.log('');
			let errEqNeeded = ' ================';
			for (let i = 0, l = errName.length; i < l; i++) {
				errEqNeeded += '=';
			}
			console.log(chalk.bgRed(errEqNeeded + ' '));
			console.log(chalk.bgRed(` <<<< ERROR ${errName} >>>> `));
			console.log(chalk.bgRed(errEqNeeded + ' '));
			console.log('');
		},
		end: () => {
			console.log('');
			console.log(chalk.bgRed(' =================== '));
			console.log(chalk.bgRed(` <<<< ERROR END >>>> `));
			console.log(chalk.bgRed(' =================== '));
			console.log('');
		}
	},
	success: (message, time) => {
		console.log(chalk.cyan(Time.getTime()) + ' ' + chalk.green(message) + ' in ' + chalk.magenta(time) + 'ms');
	},
	watch: {
		init: () => {
			console.log('');
			console.log(chalk.bgBlue(' ============================== '));
			console.log(chalk.bgBlue(' <<<< WATCHING FOR CHANGES >>>> '));
			console.log(chalk.bgBlue(' ============================== '));
			console.log('');
		},
		change: (event, message) => {
			console.log(chalk.cyan(Time.getTime()) + ' ' + chalk.bgMagenta(' ' + event.type + ' ') + ' ' + path.basename(event.path) + ' ' + chalk.green(message));
		}
	} 
}