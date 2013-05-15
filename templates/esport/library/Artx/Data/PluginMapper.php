<?php

ArtxLoadClass('Artx_Data_Mapper');

class Artx_Data_PluginMapper extends Artx_Data_Mapper
{
    function Artx_Data_PluginMapper()
    {
        parent::Artx_Data_Mapper('plugin', 'plugins', 'id');
    }

    function & find($filter = array())
    {
        $where = array();
        if (isset($filter['element']))
            $where[] = 'element = ' . $this->_db->Quote($this->_db->getEscaped($filter['element'], true), false);
        $result = & $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    function & create()
    {
        $row = & $this->_create();
        return $row;
    }
}
