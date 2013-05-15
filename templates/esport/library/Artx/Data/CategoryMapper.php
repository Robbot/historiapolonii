<?php

ArtxLoadClass('Artx_Data_Mapper');

class Artx_Data_CategoryMapper extends Artx_Data_Mapper
{
    function Artx_Data_CategoryMapper()
    {
        parent::Artx_Data_Mapper('category', 'categories', 'id');
    }

    function & find($filter = array())
    {
        $where = array();
        if (isset($filter['section'])) {
            if ('com_content' == $filter['section'])
                $where[] = 'section NOT LIKE "%com_%"';
            else
                $where[] = 'section = ' . $this->_db->Quote($filter['section']);
        }
        if (isset($filter['title']))
            $where[] = 'title = ' . $this->_db->Quote($this->_db->getEscaped($filter['title'], true), false);

        $result = & $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0, true);
        return $result;
    }

    function & create()
    {
        $row = & $this->_create();
        $row->image_position = 'left';
        $row->published = '1';
        $row->ordering = '1';
        return $row;
    }

    function delete($id)
    {
        $status = $this->_cascadeDelete('content', array('category' => $id));
        if (is_string($status))
            return $this->_error($status, 1);
        return parent::delete($id);
    }
}
