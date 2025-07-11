<?php

use Spatie\Html\Html;

if (! function_exists('html')) {
    /**
     * @return \Spatie\Html\Html
     */
    function html()
    {
        return app(Html::class);
    }
}
