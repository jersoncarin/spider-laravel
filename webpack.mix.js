const mix = require('laravel-mix');
const del = require('del');

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

mix.copyDirectory('resources/css', 'public/frontend/static/css'); 

mix.js('resources/js/frontend.min.js','public/frontend/static/js');

mix.scripts([
    'resources/js/admin/jquery.js',
    'resources/js/admin/jquery.easing.js',
    'resources/js/admin/bootstrap.bundle.js',
    'resources/js/admin/Chart.bundle.js',
    'resources/js/admin/jquery.dataTables.js',
    'resources/js/admin/dataTables.bootstrap4.js',
    'resources/js/admin/sb-admin-2.js',
    'resources/js/admin/admin.js'
], 'public/frontend/static/js/admin.min.js');

mix.styles([
    'resources/css/admin/fontawesome-all.css',
    'resources/css/admin/sb-admin-2.css',
    'resources/css/admin/dataTables.bootstrap4.css'
], 'public/frontend/static/css/admin.min.css');

mix.copyDirectory('resources/css', 'public/frontend/static/css');

mix.styles([
    'public/frontend/static/css/black-dashboard.css',
    'public/frontend/static/css/nucleo-icons.css',
    'public/frontend/static/css/frontend.css',
    'public/frontend/static/css/simple-scrollbar.css',
    'node_modules/normalize.css/normalize.css'
], 'public/frontend/static/css/all.min.css').then(() => {
    del('public/frontend/static/css/black-dashboard.css');
    del('public/frontend/static/css/nucleo-icons.css');
    del('public/frontend/static/css/frontend.css');
    del('public/frontend/static/css/simple-scrollbar.css');
    del('public/frontend/static/css/admin');
});

/*
mix.copyDirectory('resources/img', 'public/frontend/static/images');
mix.copyDirectory('resources/fonts', 'public/frontend/static/fonts'); */
mix.copyDirectory('resources/webfonts', 'public/frontend/static/webfonts');