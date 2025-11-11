const mix = require("laravel-mix");

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
  .babel(
    [
      "node_modules/sortablejs/Sortable.min.js",
      "resources/assets/js/jquery-ui.min.js",
      "resources/assets/js/huebee.js",
      "resources/assets/js/app.js",
      "resources/assets/js/keyBindings.js",
      "resources/assets/js/itemExport.js",
      "resources/assets/js/itemImport.js",
      "resources/assets/js/liveStatRefresh.js",
    ],
    "public/js/app.js"
  )
  .sass("resources/assets/sass/app.scss", "public/css")
  .options({
    processCssUrls: false,
  })
  .version();
