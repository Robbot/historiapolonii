<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XGallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class XGalleryModelCategories extends JModel {

	var $_data = null;
	var $_pagination = null;
	var $_total = null;
	var $_search = null;
	var $_query = null;
	var $_filter_order = null;
	
	function &getData() {
		$pagination =& $this->getPagination();
		
		if (empty($this->_data)) {
			$query = $this->buildSearch();
			$this->_data = $this->_getList($query, $pagination->limitstart, $pagination->limit);
		}

		$this->_total = count($this->_data);				
		
		if($this->_filter_order == 'ordering') {			
			// Order Categories to tree
			$tree = array();
			$this->_data = GalleryHelper::getCategoryTree($this->_data, $tree, 0, -1);
		}
		
		return $this->_data;
	}
	
	function buildSearch() {
		if (!$this->_query) {
			$search = $this->getSearch();

			$this->_query = 	"SELECT a.*, c.countid AS countid";
			$this->_query .=	" FROM #__xgallery_categories AS a";
			$this->_query .=	" JOIN (SELECT c.pid, count(*) AS countid";
			$this->_query .=	" FROM #__xgallery_categories AS c";
			$this->_query .=	" GROUP BY c.pid) AS c";
			$this->_query .=	" ON a.pid = c.pid";

			$this->_query .= $this->_buildContentWhere();
			$this->_query .= $this->_buildContentOrderBy();
		}

		return $this->_query;
	}
	
	/**
	 * Method to build the where clause of the query
	 **/
	function _buildContentWhere() {
		global $mainframe, $option;

		$params 			= & JComponentHelper::getParams('com_xgallery');
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_state', 		'filter_state', '', 'cmd' );
		$filter_type 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_type', 		'filter_type', 	'', 'cmd' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.categories.search', 			'search', 		'', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
		$filter_cat 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_cat', 		'filter_cat', 	'-1', 'cmd' );
	
		$where = array();
		
		if ( $filter_cat != '-1' ) {
			$where[] = 'pid = '.$filter_cat;
		}

		if ( $filter_state != '') {
			$where[] = 'published = '.$filter_state;
		}
		
		if ($search) {
			if (!$filter_type) {
				$where[] = ' LOWER(a.name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(a.quicktake) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(a.description) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 1) {
				$where[] = ' LOWER(a.name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 2) {
				$where[] = ' LOWER(a.quicktake) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(a.description) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 3) {
				$where[] = ' LOWER(a.name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function _buildContentOrderBy() {
        global $mainframe, $option;
 
        $this->_filter_order = $filter_order     = $mainframe->getUserStateFromRequest( $option.'.categories.filter_order', 'filter_order', 'ordering', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'.categories.filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 		
		if($filter_order == '' && $filter_order_Dir == '') {
        	$orderby = ' ORDER BY ' . $filter_order.' '.$filter_order_Dir;
        } else if($filter_order == 'ordering') {
        	$orderby = ' ORDER BY ordering '.$filter_order_Dir;
        } else {
        	$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
        }
         		 
        return $orderby;
	}
	
	function getCategory() {
		$query = "SELECT * FROM #__xgallery_categories";
		$cdata = $this->_getList($query);
		$list = array();
		
		if(empty($cdata)) {
			JError::raiseError(404, JTEXT::_('CATEGORY NOT FOUND'));
		}
		
		$list[0] = '';
		foreach($cdata as $data) {		
			$list[$data->id] = $data->name;
		}
		return $list;
	}
	
	function getTotal() {
		if (!$this->_total) {
			$query = $this->buildSearch();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	function &getPagination() {
		if(!$this->_pagination) {
			jimport('joomla.html.pagination');
			global $mainframe;
			$this->_pagination = new JPagination($this->getTotal(), JRequest::getVar('limitstart', 0), JRequest::getVar('limit', $mainframe->getCfg('list_limit')));
		}

		return $this->_pagination;
	}
	
	function getSearch() {
		if (!$this->_search) {
			global $mainframe, $option;

			$search = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
			$this->_search = JString::strtolower($search);
		}

		return $this->_search;
	}	
}