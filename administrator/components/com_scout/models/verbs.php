<?php
/**
 * @version	1.5
 * @package	Scout
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_scout.models._base', JPATH_ADMINISTRATOR.DS.'components' );

class ScoutModelVerbs extends ScoutModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');
        $filter_id_from	= $this->getState('filter_id_from');
        $filter_id_to	= $this->getState('filter_id_to');
        $filter_name	= $this->getState('filter_name');
        $filter_value   = $this->getState('filter_value');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.verb_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.verb_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.verb_value) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
        	{
        		$query->where('tbl.verb_id >= '.(int) $filter_id_from);	
        	}
        		else
        	{
        		$query->where('tbl.verb_id = '.(int) $filter_id_from);
        	}
       	}
       	
		if (strlen($filter_id_to))
        {
        	$query->where('tbl.verb_id <= '.(int) $filter_id_to);
       	}
       	
    	if (strlen($filter_name))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
        	$query->where('LOWER(tbl.verb_name) LIKE '.$key);
       	}
       	
        if (strlen($filter_value))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_value ) ) ).'%');
            $query->where('LOWER(tbl.verb_value) LIKE '.$key);
        }
    }
        	
	public function getList()
	{
		$list = parent::getList(); 
		foreach(@$list as $item)
		{
			$item->link = 'index.php?option=com_scout&controller=verbs&view=verbs&task=edit&id='.$item->verb_id;
		}
		return $list;
	}
}
