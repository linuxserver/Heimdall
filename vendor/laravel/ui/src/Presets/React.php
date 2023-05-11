<?php

namespace Laravel\Ui\Presets;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class React extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::ensureComponentDirectoryExists();
        static::updatePackages();
        static::updateWebpackConfiguration();
        static::updateBootstrapping();
        static::updateComponent();
        static::removeNodeModules();
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
            '@babel/preset-react' => '^7.0.0',
            'react' => '^16.2.0',
            'react-dom' => '^16.2.0',
        ] + Arr::except($packages, ['vue', 'vue-template-compiler']);
    }

    /**
     * Update the Webpack configuration.
     *
     * @return void
     */
    protected static function updateWebpackConfiguration()
    {
        copy(__DIR__.'/react-stubs/webpack.mix.js', base_path('webpack.mix.js'));
    }

    /**
     * Update the example component.
     *
     * @return void
     */
    protected static function updateComponent()
    {
        (new Filesystem)->delete(
            resource_path('js/components/ExampleComponent.vue')
        );

        copy(
            __DIR__.'/react-stubs/Example.js',
            resource_path('js/components/Example.js')
        );
    }

    /**
     * Update the bootstrapping files.
     *
     * @return void
     */
    protected static function updateBootstrapping()
    {
        copy(__DIR__.'/react-stubs/app.js', resource_path('js/app.js'));
    }
}
