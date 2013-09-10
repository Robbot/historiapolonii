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

class ScoutModelLogs extends ScoutModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
        $filter_id_from	    = $this->getState('filter_id_from');
        $filter_id_to	    = $this->getState('filter_id_to');
        $filter_date_from   = $this->getState('filter_date_from');
        $filter_date_to     = $this->getState('filter_date_to');
        $filter_subject     = $this->getState('filter_subject');
        $filter_verb        = $this->getState('filter_verb');
        $filter_object      = $this->getState('filter_object');
        $filter_scopeid     = $this->getState('filter_scopeid');
        $filter_subjectid   = $this->getState('filter_subjectid');
        $filter_verbid      = $this->getState('filter_verbid');
        $filter_objectid    = $this->getState('filter_objectid');
        $filter_client      = $this->getState('filter_client');
        $filter_scope       = $this->getState('filter_scope');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.log_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.subject_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.verb_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.object_id) LIKE '.$key;
			$where[] = 'LOWER(subject.subject_name) LIKE '.$key;
			$where[] = 'LOWER(verb.verb_name) LIKE '.$key;
			$where[] = 'LOWER(object.object_name) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
        	{
        		$query->where('tbl.log_id >= '.(int) $filter_id_from);	
        	}
        		else
        	{
        		$query->where('tbl.log_id = '.(int) $filter_id_from);
        	}
       	}
       	
		if (strlen($filter_id_to))
        {
        	$query->where('tbl.log_id <= '.(int) $filter_id_to);
       	}

        if (strlen($filter_subjectid))
        {
            $query->where('tbl.subject_id = '.(int) $filter_subjectid);
        }
        
        if (strlen($filter_verbid))
        {
            $query->where('tbl.verb_id = '.(int) $filter_verbid);
        }
        
        if (strlen($filter_objectid))
        {
            $query->where('tbl.object_id = '.(int) $filter_objectid);
        }
        
        if (strlen($filter_scopeid))
        {
            $query->where('scope.scope_id = '.(int) $filter_scopeid);
        }
       	
    	if (strlen($filter_subject))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_subject ) ) ).'%');
        	$query->where('LOWER(subject.subject_name) LIKE '.$key);
       	}
       	
        if (strlen($filter_verb))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_verb ) ) ).'%');
            $query->where('LOWER(verb.verb_name) LIKE '.$key);
        }
        
        if (strlen($filter_object))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_object ) ) ).'%');
            $query->where('LOWER(object.object_name) LIKE '.$key);
        }
        
        if (strlen($filter_scope))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_scope ) ) ).'%');
            $query->where('LOWER(scope.scope_name) LIKE '.$key);
        }
        
        if (strlen($filter_client))
        {
            $query->where('scope.client_id = '.(int) $filter_client);
        }
        
        if (strlen($filter_date_from))
        {
            $query->where("tbl.datetime >= '".$filter_date_from."'");
        }
        
        if (strlen($filter_date_to))
        {
            $query->where("tbl.datetime <= '".$filter_date_to."'");
        }
    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__scout_subjects AS subject ON tbl.subject_id = subject.subject_id');
        $query->join('LEFT', '#__scout_verbs AS verb ON tbl.verb_id = verb.verb_id');
        $query->join('LEFT', '#__scout_objects AS object ON tbl.object_id = object.object_id');
        $query->join('LEFT', '#__scout_scopes AS scope ON object.scope_id = scope.scope_id');
    }
    
    protected function _buildQueryFields(&$query)
    {
        $fields = array();
        $fields[] = " subject.subject_name AS subject ";
        $fields[] = " verb.verb_name as verb ";
        $fields[] = " object.object_name as object ";
        $fields[] = " scope.scope_name AS scope_name ";
        $fields[] = " scope.client_id as client_id ";
        $fields[] = " scope.scope_url as scope_url ";
        
        $query->select( $this->getState( 'select', 'tbl.*' ) );     
        $query->select( $fields );
    }
    
	public function getList()
	{
		$list = parent::getList(); 
		foreach(@$list as $item)
		{
			$item->link = 'index.php?option=com_scout&controller=logs&view=logs&task=edit&id='.$item->log_id;
		}
		return $list;
	}
}
