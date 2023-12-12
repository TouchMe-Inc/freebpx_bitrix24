<?php

namespace Dialplan;

interface DialplanHook
{
    public static function execution(&$ext);
}