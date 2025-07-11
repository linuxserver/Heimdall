<?php

namespace Spatie\Html;

interface Selectable
{
    /**
     * @return static
     */
    public function selected();

    /**
     * @param bool $condition
     * @return static
     */
    public function selectedIf($condition);

    /**
     * @return static
     */
    public function unselected();
}
