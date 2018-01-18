// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create web/build/app.js and web/build/app.css
    .addEntry('app', './app/Resources/public/js/index.js')

    // will output as web/build/global.css
    .addStyleEntry('global', [
        './app/Resources/public/css/index.css',
        './app/Resources/public/css/brands.css',
        './app/Resources/public/css/contact.css',
        './app/Resources/public/css/forms.css',
        './app/Resources/public/css/about.css'
    ])

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();