<?php

namespace chenweibo\LaravelCmsFile\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelCmsFile extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelcmsfile';
    }
}
