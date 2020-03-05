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

mix.setPublicPath('dev/maia/dist')

    /**
     * Filemanager
     */
    .js('dev/maia/resources/js/filemanager/filemanager-tool.js', 'js')
    .copy('dev/maia/dist/js/filemanager-tool.js', 'vendor/spacecode-dev/maia/dist/js/filemanager-tool.js')
    .js('dev/maia/resources/js/filemanager/filemanager-field.js', 'js')
    .copy('dev/maia/dist/js/filemanager-field.js', 'vendor/spacecode-dev/maia/dist/js/filemanager-field.js')

    /**
     * Settings
     */
    .js('dev/maia/resources/js/settings/settings.js', 'js')
    .copy('dev/maia/dist/js/settings.js', 'vendor/spacecode-dev/maia/dist/js/settings.js')

    /**
     * Seo
     */
    .js('dev/maia/resources/js/seo/seo.js', 'js')
    .copy('dev/maia/dist/js/seo.js', 'vendor/spacecode-dev/maia/dist/js/seo.js')

    /**
     * Horizon
     */
    .js('dev/maia/resources/js/horizon/horizon.js', 'js')
    .copy('dev/maia/dist/js/horizon.js', 'vendor/spacecode-dev/maia/dist/js/horizon.js')

    /**
     * Advanced Image
     */
    .js('dev/maia/resources/js/advanced-image/advanced-image.js', 'js')
    .copy('dev/maia/dist/js/advanced-image.js', 'vendor/spacecode-dev/maia/dist/js/advanced-image.js')

    /**
     * Toggle
     */
    .sass('dev/maia/resources/sass/toggle/toggle.scss', 'css')
    .copy('dev/maia/dist/css/toggle.css', 'vendor/spacecode-dev/maia/dist/css/toggle.css')

    /**
     * Bootstrap
     */
    .sass('node_modules/bootstrap/scss/bootstrap.scss', 'index')
    .copy('dev/maia/dist/index/bootstrap.css', 'vendor/spacecode-dev/maia/dist/index/bootstrap.css')

    /**
     * Vendor
     */
    .js('dev/maia/resources/js/vendor.js', 'index')
    .copy('dev/maia/dist/index/vendor.js', 'vendor/spacecode-dev/maia/dist/index/vendor.js')
;

mix
    .sass('resources/sass/app.scss', 'assets/css')
    .js('resources/js/app.js', 'assets/js')
    .babel([
        'resources/js/app.js',
        'node_modules/inputmask/dist/jquery.inputmask.js',
        'node_modules/owl.carousel/dist/owl.carousel.js',
        'node_modules/bootstrap-notify/bootstrap-notify.js'
    ], 'public/assets/js/app.js');

mix.options({
    processCssUrls: false,
    postCss: [
        require('autoprefixer')({
            browsers: ['last 2 versions'],
            cascade: false
        })
    ]
});
