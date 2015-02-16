<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 19:54
 */

interface ConfigInterface
{
    function get($key, $default = null);
}