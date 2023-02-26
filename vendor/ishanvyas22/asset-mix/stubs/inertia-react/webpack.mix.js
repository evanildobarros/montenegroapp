const mix = require('laravel-mix');

mix.setPublicPath('./webroot')
    .react('assets/js/app.js', 'webroot/js')
    .sass('assets/sass/app.scss', 'webroot/css')
    .webpackConfig({
        output: {
            chunkFilename: 'js/[name].js?id=[chunkhash]',
        },
        resolve: {
            extensions: ['.js', '.jsx'],
            alias: {
                app: __dirname + '/assets/js',
            },
        },
    })
    .version()
    .sourceMaps();
