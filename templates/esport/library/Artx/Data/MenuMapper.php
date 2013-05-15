<?php

ArtxLoadClass('Artx_Data_Mapper');

class Artx_Data_MenuMapper extends Artx_Data_Mapper
{
    function Artx_Data_MenuMapper()
    {
        parent::Artx_Data_Mapper('menutypes', 'menu_types', 'id');
    }

    function & find($filter = array())
    {
        $where = array();
        if (isset($filter['title']))
            $where[] = 'title = ' . $this->_db->Quote($this->_db->getEscaped($filter['title'], true), false);
        $result = & $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    function & create()
    {
        $row = & $this->_create();
        return $row;
    }

    function delete($id)
    {
        $menu = & $this->fetch($id);
        if (is_string($menu))
            return $this->_error($menu, 1);
        $status = $this->_cascadeDelete('menuItem', array('menu' => $menu->menutype));
        if (is_string($status))
            return $this->_error($status, 1);
        return parent::delete($id);
    }
}
