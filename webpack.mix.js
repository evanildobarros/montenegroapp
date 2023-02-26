const mix = require('laravel-mix');

mix
    .autoload({
        jquery: ['$', 'jQuery', 'jquery', 'window.jQuery'],
        moment: 'moment'
    })
    .setPublicPath('./webroot')
    .babelConfig({
        plugins: ['@babel/plugin-syntax-dynamic-import'],
    })
    .webpackConfig({
        devtool: 'inline-source-map',
        output: {
            chunkFilename: 'js/[name].js?id=[chunkhash]',
        },
        resolve: {
            extensions: ['.js', '.ts', '.tsx', '.jsx'],
            alias: {
                app: __dirname + '/assets/js',
            },
        },
    })
    .js('assets/js/app.js', 'webroot/js')
    .sass('assets/sass/app.scss', 'webroot/css')
    .sourceMaps()
    .version();
