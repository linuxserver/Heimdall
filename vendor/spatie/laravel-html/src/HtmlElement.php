<?php

namespace Spatie\Html;

interface HtmlElement
{
    /**
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function render();
}
