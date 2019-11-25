const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.autoload({
    jquery: ['$', 'jQuery', 'window.jQuery'],
});

mix.js('resources/js/custom.js', 'public/js')
    .styles('resources/css/login.css', 'public/css/login.css')
    .styles('resources/css/style.css', 'public/css/style.css');
