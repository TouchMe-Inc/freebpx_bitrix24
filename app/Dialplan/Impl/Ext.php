<?php

namespace Dialplan\Impl;

use Dialplan\DialplanHook;

class Ext implements DialplanHook
{

    public static function execution(&$ext)
    {
        $ext->add('ext-local', 's', '', new \ext_noop("Noop Test"));
    }
}