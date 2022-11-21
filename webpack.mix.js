const path = require('path');
const glob = require('glob-all');
const PurgecssPlugin = require('purgecss-webpack-plugin');
let mix = require('laravel-mix');
require('laravel-mix-purgecss');

mix.webpackConfig(webpack => {
    return {
        resolve: {
            alias: {
                ziggy: path.resolve('vendor/tightenco/ziggy/dist/js/route.js'),
            },
        },
        plugins: [
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery'
            })
        ]
    };
});
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

mix
	.js('./node_modules/@fortawesome/fontawesome-free/js/all.min.js', 'public/js')
	.js('resources/js/app.js', 'public/js')
	.js('resources/js/card.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .version();
