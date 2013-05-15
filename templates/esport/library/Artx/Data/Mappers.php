<?php

ArtxLoadClass('Artx_Data_CategoryMapper');
ArtxLoadClass('Artx_Data_ContentMapper');
ArtxLoadClass('Artx_Data_MenuItemMapper');
ArtxLoadClass('Artx_Data_MenuMapper');
ArtxLoadClass('Artx_Data_ModuleMapper');
ArtxLoadClass('Artx_Data_PluginMapper');
ArtxLoadClass('Artx_Data_SectionMapper');

class Artx_Data_Mappers
{
    function & errorCallback(&$callback, $get = false)
    {
        static $errorCallback;
        if (!$get)
            $errorCallback = $callback;
        return $errorCallback;
    }

    function & get($name)
    {
        $className = 'Artx_Data_' . ucfirst($name) . 'Mapper';
        $mapper = new $className();
        return $mapper;
    }

    function error($error, $code)
    {
        $null = null;
        $callback = & Artx_Data_Mappers::errorCallback($null, true);
        if (isset($callback))
            call_user_func($callback, $error, $code);
        return $error;
    }
}
