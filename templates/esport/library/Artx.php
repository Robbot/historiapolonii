<?php

function ArtxLoadClassesFromDir($prefix, $path, $get = false)
{
    static $dirs;
    if (!$get)
        $dirs[$prefix] = $path;
    return isset($dirs[$prefix]) ? $dirs[$prefix] : null;
}

function ArtxLoadClass($class)
{
    if (class_exists($class))
        return;
    $separator = strrpos($class, '_');
    $prefix = substr($class, 0, $separator);
    $name = substr($class, $separator + 1);
    $path = ArtxLoadClassesFromDir($prefix, null, true);
    if (is_string($path))
        require_once $path . DIRECTORY_SEPARATOR . $name . '.php';
    else
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
}
