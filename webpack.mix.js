var mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js');
mix.js('resources/assets/js/sidebar.js', 'public/js');
//mix.js('resources/assets/js/math.js', 'public/js');
//mix.options({ purifyCss: true });
//mix.sass('resources/assets/sass/app.scss', 'public/css/coin.min.css');
//mix.sass('resources/assets/sass/edu.scss', 'public/css/edu.min.css');
//mix.sass('resources/assets/sass/qa.scss', 'public/css/qa.min.css');
