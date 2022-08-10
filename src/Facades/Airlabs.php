<?php

namespace Ezzaze\Airlabs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ezzaze\Airlabs\Airlabs
 */
class Airlabs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Airlabs';
    }
}
