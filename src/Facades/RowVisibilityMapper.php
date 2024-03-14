<?php

namespace Jgu\RowVisibilityMapper\Facades;

use Illuminate\Support\Facades\Facade;

class RowVisibilityMapper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'row-visibility-mapper';
    }
}
