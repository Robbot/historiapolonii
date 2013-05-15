<?php

ArtxLoadClass('Artx_Data_Mapper');

class Artx_Data_ContentMapper extends Artx_Data_Mapper
{
    function Artx_Data_ContentMapper()
    {
        parent::Artx_Data_Mapper('content', 'content', 'id');
    }

    function & find($filter = array())
    {
        $where = array();
        if (isset($filter['section']))
            $where[] = 'sectionid = ' . intval($filter['section']);
        if (isset($filter['category']))
            $where[] = 'catid = ' . intval($filter['category']);
        if (isset($filter['title']))
            $where[] = 'title = ' . $this->_db->Quote($this->_db->getEscaped($filter['title'], true), false);
        $result = & $this->_loadObjects($where, isset($filter['limit']) ? (int)$filter['limit'] : 0);
        return $result;
    }

    function & create()
    {
        $row = & $this->_create();
        $row->state = '1';
        $row->version = '1';
        $config = & JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');
        $date = & JFactory::getDate('now', $tzoffset);
        $row->created = $date->toMySQL();
        $row->publish_up = $date->toMySQL();
        $row->publish_down = $this->_db->getNullDate();
        return $row;
    }

    function save(&$row)
    {
        JPluginHelper::importPlugin('content');

        $isNew = (bool)$row->id;
        if (!$row->check())
            return $this->_error($row->getError(), 1);
        $dispatcher = & JDispatcher::getInstance();
        $result = $dispatcher->trigger('onBeforeContentSave', array(&$row, $isNew));
        if(in_array(false, $result, true))
            return $this->_error($row->getError(), 1);
        if (!$row->store())
            return $this->_error($row->getError(), 1);
        $row->checkin();
        $row->reorder('catid = ' . (int)$row->catid . ' AND state >= 0');
        $cache = & JFactory::getCache('com_content');
        $cache->clean();
        $dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));
        return null;
    }
}
