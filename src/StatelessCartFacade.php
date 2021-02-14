<?php

namespace Webtamizhan\StatelessCart;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webtamizhan\StatelessCart\StatelessCart
 */
class StatelessCartFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stateless-cart';
    }
}
