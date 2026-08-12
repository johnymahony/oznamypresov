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

const tailwindcss = require('tailwindcss')

mix.js('resources/assets/js/app.js', 'public/js');

// mix.sass('resources/assets/sass/app.scss', 'public/css')
//     .options({
//       processCssUrls: false,
//       postCss: [ tailwindcss('tailwind.config.js') ],
//     });

let cities = ['presov', 'zilina', 'hlohovec', 'banskabystrica', 'trencin', 'poprad'];
for (let city of cities) {
    mix.sass('resources/assets/sass/' + city + '.scss', 'public/css')
        .options({
            processCssUrls: false,
            postCss: [tailwindcss('tailwind.config.js')],
        });
}

if (mix.inProduction()) {
    mix.version();
}