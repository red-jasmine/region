<?php

namespace RedJasmine\Region\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \RedJasmine\Region\Region
 */
class Region extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'region';
    }
}
