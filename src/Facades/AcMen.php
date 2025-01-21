<?php

namespace Wilfreedi\AcMen\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static array sendMessage($chatId, $message)
 *
 */

class AcMen extends Facade
{
    protected static function getFacadeAccessor(): string {
        return 'acmen';
    }
}
