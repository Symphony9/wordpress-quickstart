# Get started

## Note: Try [yarn](http://yarnpkg.com) instead of npm. Its faster. Especialy on windows

## Prerequisities
- __yarn__ or __npm__
- I will be using yarn

## Local development
1. Clone repo
2. Set up virtualhost on your system
3. Create database
4. (__Optional__) Get DB dump somewhere and import it to your DB
5. Duplicate wp-config-sample.php - `cp wp-config-sample.php wp-config.php`
6. Edit __wp-config.php__ - fill in your db info
7. (__Optional__...or if compiling does not work) Install gulp globally (You should not need this but just for safe measures) - `yarn global add gulp-cli gulp`
8. Install FE dependencies - `yarn`
9. a. Run watcher for compiling SCSS and JS - `yarn start`
	b. If you need production ready bundle - `yarn run build`
	c. If above does not work try step 7) of this guide. Otherwise ask Vojta if he knows how to fix your problem
10. Happy coding!

## What is included?

### JS libraries
- [axios](https://github.com/axios/axios) for promise based ajax
- [pickathing](https://github.com/Symphony9/pickathing) for nice selects

### WP Plugins
- Custom wp plugin for form ajax - see implementation example in `wp-content/plugins/contact-form`
- [ACF Pro](https://www.advancedcustomfields.com/resources/) for awesome custom fields in WP admin

### other stuff
- `get_view()` custom functions to include template parts with data - see `wp-content/themes/theme/functions`
- Kint PHP debugger (var_dumper) - just use `d(expression)`
