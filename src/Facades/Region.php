<?php

namespace RedJasmine\Region\Facades;

use Illuminate\Support\Facades\Facade;

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
