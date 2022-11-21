const mix = require('laravel-mix');

mix.webpackConfig({
    module: {
        exprContextCritical: false,
        rules: [
            {
                test: /\.js?$/,
                use: [{
                    loader: 'babel-loader',
                    options: mix.config.babel()
                }]
            }
        ]
    }
})
.options({
	processCssUrls: false,
    cssNano: {
        discardComments: {
            removeAll: true
        },
        discardDuplicates: true,
        discardEmpty: true,
    }
})

mix.autoload({
    'jquery': ['$', 'window.jQuery', 'jQuery', 'jquery']
})

mix.sass('resources/assets/sass/app.scss', 'publishable/assets/css', {
	implementation: require('node-sass') 
})
.js('resources/assets/js/app.js', 'publishable/assets/js')
.copy('node_modules/tinymce/skins', 'publishable/assets/js/skins')
.copy('resources/assets/js/skins', 'publishable/assets/js/skins')
.copy('node_modules/tinymce/themes/modern', 'publishable/assets/js/themes/modern')
.copy('node_modules/ace-builds/src-noconflict', 'publishable/assets/js/ace/libs');