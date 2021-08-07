<?php

if (!function_exists('my_style_supported'))
{
    function my_style_supported($object): bool
    {
        return $object && in_array(get_class($object), config('plugins.my-style.config.supported'));
    }
}