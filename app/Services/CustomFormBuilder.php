<?php

namespace App\Services;

use Spatie\Html\Html;
use Illuminate\Support\HtmlString;

class CustomFormBuilder
{
    protected Html $html;

    public function __construct(Html $html)
    {
        $this->html = $html;
    }

    public function text($name, $value = null, $options = [])
    {
        return new HtmlString(
            $this->html->input('text', $name, $value)->attributes($options)
        );
    }

    public function select($name, $list = [], $selected = null, $options = [])
    {
        return new HtmlString(
            $this->html->select($name)->options($list, $selected)->attributes($options)
        );
    }

    public function textarea($name, $value = null, $options = [])
    {
        return new HtmlString(
            $this->html->textarea($name, $value)->attributes($options)
        );
    }

    // Add other methods as needed
}
