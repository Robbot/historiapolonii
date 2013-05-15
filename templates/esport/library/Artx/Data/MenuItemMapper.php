<?php

ArtxLoadClass('Artx_Data_Mapper');

class Artx_Data_MenuItemMapper extends Artx_Data_Mapper
{
    function Artx_Data_MenuItemMapper()
    {
        parent::Artx_Data_Mapper('menu', 'menu', 'id');
    }

    function & find($filter = array())
    {
        $where = array();
        if (isset($filter['menu']))
            $where[] = 'menutype = ' . $this->_db->Quote($filter['menu']);
        if (isset($filter['title']))
            $where[] = 'name = ' . $this->_db->Quote($filter['title']);
        if (isset($filter['home']))
            $where[] = 'home = ' . $this->_db->Quote($filter['home']);
        $result = & $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    function & create()
    {
        $row = & $this->_create();
        $row->published = '1';
        $row->parent = '0';
        return $row;
    }
}
