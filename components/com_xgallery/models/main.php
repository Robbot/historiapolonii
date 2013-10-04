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
defined('_JEXEC') or die ('Restricted access');
jimport('joomla.application.component.model');

class XGalleryModelMain extends JModel {
	var $_data = null;
	var $_pagination = null;
	var $_total = null;
	var $_query = null;
	var $_search = null;
	var $_id = null;
	var $_display = null;
	var $_sort_dir = null;
	var $_cat = null;
	var $_catslug = null;
	var $_collslug = null;
	
	function __construct() {
		parent::__construct();
		
		global $mainframe, $option;
		$params =& $mainframe->getParams();
		
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $params->get('main_img_per_page', $mainframe->getCfg('list_limit')), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		//In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->_display = $params->get('main_coll_sort_order', 'latest');
		$this->_sort_dir = $params->get('main_coll_sort_order_dir', 'asc');
	}
		
	function &getData() {			
		global $option;
		
		if(empty($this->_data)) {			
			$this->_id = JRequest::getInt('id', 1);
			$query = $this->_buildSearch();

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_total = $this->_getListCount($query);

			if(empty($this->_data)) {
				$this->_data = array();
			}
		}
		
		if(!$this->_catslug) {
			$this->_catslug = GalleryHelper::getCategorySlug();
		}
		
		if(!$this->_collslug) {
			$this->_collslug = GalleryHelper::getCollectionSlug();
		}
		
		$count = count($this->_data);
		for($i = 0; $i < $count; $i++) {
			$this->_data[$i]->slug = $this->_data[$i]->id . ':' . $this->_collslug;
			$this->_data[$i]->catslug = $this->_data[$i]->cid . ':' . $this->_catslug;
		}
		
		return $this->_data;
	}
			
	function getCategory() {
		global $option;
		$access = GalleryHelper::getAccessLevel();
		$query = "SELECT * FROM #__xgallery_categories WHERE published = '1' AND id ='{$this->_id}'".$access." LIMIT 1";
		$cdata = $this->_getList($query);
		if(empty($cdata)) {
			JError::raiseError('404', JText::_('CATEGORY NOT FOUND'));
		}
		return $cdata;
	}
		
	/**
	 *  Function to get all sub cagegories in the current categories that is allowed for the 
	 *  visitors access level.
	 *  
	 *  @return catids The sub category ids
	 */
	function _getCategories() {
		$catids = array();
		$rows = array();
		$access = GalleryHelper::getAccessLevel();

		if(empty($this->_catids)) {

			if($this->_id == null) {
				$this->_id = JRequest::getInt('id');
			}
			
			if($this->_id != 1) {
				$catids[] = $this->_id;
				$query = "SELECT id FROM #__xgallery_categories WHERE published = '1' AND pid= '{$this->_id}'".$access;
				$db =& JFactory::getDBO();
				$db->setQuery($query);
				$rows = $db->loadResultArray();

				while(count($rows) > 0) {
					$catArray = array();				
				
					foreach($rows as $row) {
						$catids[] = $row;
						$catArray[] = " pid='{$row}'";					
					}
				
					$query = "SELECT id FROM #__xgallery_categories WHERE published = '1'";
					if(count($catArray) > 0) {
						$query .= ' AND ('. implode(' OR ', $catArray).')';
					}
					$query .= $access;
					$db->setQuery($query);
					$rows = array();
					$rows = $db->loadResultArray();	
		
				}
			}
			
		} 	
		
		if(count($catids) > 0) {
			$this->_catids = $catids;
		}
		
		return $this->_catids;
	}
	
	/**
	 *  Function to get all sub cagegories in the current categories that is allowed for the 
	 *  visitors access level.
	 *  
	 *  @return catids The sub category ids
	 */
	function _getCollCategories() {
		$catids = array();
		$rows = array();
		$access = GalleryHelper::getAccessLevel();

		if(empty($this->_catids)) {
			if($this->_id == 1) {
				$catids[] = $this->_id;
				$query = "SELECT id FROM #__xgallery_categories WHERE published = '1' AND pid= '1'".$access;
				$db =& JFactory::getDBO();
				$db->setQuery($query);
				$rows = $db->loadResultArray();

				while(count($rows) > 0) {
					$catArray = array();				
				
					foreach($rows as $row) {
						$catids[] = $row;
						$catArray[] = " pid='{$row}'";					
					}
				
					$query = "SELECT id FROM #__xgallery_categories WHERE published = '1'";
					if(count($catArray) > 0) {
						$query .= ' AND ('. implode(' OR ', $catArray).')';
					}
					$query .= $access;
					$db->setQuery($query);
					$rows = array();
					$rows = $db->loadResultArray();
				}
			}
			
		} 	

		if(count($catids) > 0) {
			$this->_catids = $catids;
		}
		
		return $this->_catids;
	}
	
	function getSubCategories() {
		global $mainframe;
		$params =& $mainframe->getParams();
		$cfgParams = &JComponentHelper::getParams( 'com_xgallery' );
		$where = $this->_getMenuPCategories();
		
		$query = 	"SELECT a.*, c.countid AS countid";
		$query .=	" FROM #__xgallery_categories AS a";
		$query .=	" JOIN (SELECT c.pid, count(*) AS countid";
		$query .=	" FROM #__xgallery_categories AS c";
		$query .=	" GROUP BY c.pid) AS c";
		$query .=	" ON a.pid = c.pid";
		
		if(count($where) > 0) {
			$query .= ' AND ('. implode(' OR ', $where).')';			
		} 
		
		$query .= " AND a.id != '1'";
		$query .= " AND a.published = '1'";
		$query .= GalleryHelper::getAccessLevel();
		
		switch($params->get('main_cat_sort_order')) {				
			case 'ordering':
				$query .= " ORDER BY ordering ".$params->get('main_cat_sort_order_dir');
				break;
			case 'random':
				$query .= " ORDER BY RAND()";
				break;
			case 'popular':
				$query .= " ORDER BY a.hits ".$params->get('main_cat_sort_order_dir');
				break;
			case 'latest':
			default:
				$query .= " ORDER BY a.creation_date ".$params->get('main_cat_sort_order_dir');	
				break;					
		}
				
		if($params->get('main_cat_limit') != 0) {
			$query .= " LIMIT ".$params->get('main_cat_limit');
		}		

		$rows = $this->_getList($query);
		
		if(count($rows) == 0) {
			$rows = array();
		} else {
			if($params->get('main_cat_sort_order') == 'ordering') {			
				// Order Categories to tree
				$tree = array();
				$rows = GalleryHelper::getCategoryTree($rows, $tree, 1, -1);
			}
		}
		
		$count = count($rows);
		
		if(!$this->_catslug) {
			$this->_catslug = GalleryHelper::getCategorySlug();
		}
		
		for($i = 0; $i < $count; $i++) {
			$rows[$i]->slug = $rows[$i]->id . ':' . $this->_catslug;								
		}
		
		return $rows;
	}
	
	function _getMenuCategories() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$access = GalleryHelper::getAccessLevel();
		$query = "SELECT link FROM #__menu WHERE published = '1' AND componentid='{$com->id}'".$access;
		$links = $this->_getList($query);

		$where = array();
		if(count($links) > 0) {
			$where[] = "pid = '1'";
			$cid = '';
			foreach($links as $link) {
				$currURI = JURI::getInstance($link->link);
				$cid = $currURI->getVar('id');
				if(!empty($cid)) {
					$where[] = "cid = '".$currURI->getVar('id')."'";
				}
			}
		}
		
		return $where;
	}
	
	function _getMenuPCategories() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$access = GalleryHelper::getAccessLevel();
		$query = "SELECT link FROM #__menu WHERE published = '1' AND componentid='{$com->id}'".$access;
		$links = $this->_getList($query);

		$where = array();
		if(count($links) > 0) {
			$where[] = "a.pid = '1'";
			$cid = '';
			foreach($links as $link) {
				$currURI = JURI::getInstance($link->link);
				$cid = $currURI->getVar('id');
				if(!empty($cid)) {
					$where[] = "a.pid = '".$currURI->getVar('id')."'";
				}
			}
		}
		
		return $where;
	}
	
	function _getMenuSubCategories() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$access = GalleryHelper::getAccessLevel();
		$query = "SELECT link FROM #__menu WHERE published = '1' AND componentid='{$com->id}'".$access;
		$links = $this->_getList($query);

		$where = array();
		if(count($links) > 0) {
			$where[] = "pid = '1'";
			$cid = '';
			foreach($links as $link) {
				$currURI = JURI::getInstance($link->link);
				$cid = $currURI->getVar('id');
				if(!empty($cid)) {
					$where[] = "pid = '".$currURI->getVar('id')."'";
				}
			}
		}
		
		return $where;
	}
	
	function getBreadcrumbs() {
		global $mainframe;
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$db =& JFactory::getDBO();		
		$query = "SELECT id, name, link FROM #__menu WHERE published = '1' AND componentid='{$com->id}' AND link LIKE '%main%'";		
		$db->setQuery($query);
		$main = $db->loadRow();
		
		$menu = $this->_getMenuItems();
		$pathway = &$mainframe->getPathway();
		unset($pathway->_pathway);
		$pathway->addItem($main[1], JRoute::_($main[2].'&amp;Itemid='.$main[0]));		
		
		$breadcrumbs = $pathway->getPathway();
				
		if(count($breadcrumbs) > 0) {
			$count = count($breadcrumbs) - 1;
	
			$currURI =& JURI::getInstance($breadcrumbs[$count]->link);
			$itemid = $currURI->getVar('Itemid');
		}
	}
	
	function _getMenuItems() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');

		$query = "SELECT id, link, parent FROM #__menu WHERE published = '1' AND componentid='{$com->id}' AND sublevel != '0'";		
		$links = $this->_getList($query);
		$where = array();

		if(count($links) > 0) {
			foreach($links as $link) {				
				$currURI = JURI::getInstance($link->link);
				if($currURI->getVar('id') == $this->_cat) {					
					return $link->id;
				}
			}
		}
		
		return $where;
	}
	
	function _buildSearch() {
		global $mainframe;
		$params =& $mainframe->getParams();
		$display = $this->_display;
		$idList = array(); 
		$catIds = $this->_getCollCategories();
		
		if (!$this->_query) {
			$search = $this->getSearch();
			$this->_query =  "SELECT #__xgallery.id AS id, #__xgallery.cid AS cid, #__xgallery.name AS name,";
			$this->_query .= " #__xgallery.thumb AS thumb, #__xgallery.hits AS hits, #__xgallery.creation_date AS creation_date,";
			$this->_query .= " #__xgallery.folder AS folder, #__xgallery.quicktake AS quicktake, #__xgallery.description AS description,";
			$this->_query .= " #__xgallery.ordering AS ordering, #__xgallery.groupname AS groupname";
			$this->_query .= " FROM #__xgallery, #__xgallery_categories";
			$this->_query .= " WHERE #__xgallery.published = '1' AND #__xgallery_categories.published = '1'";
			$this->_query .= " AND #__xgallery.cid = #__xgallery_categories.id";		

			if(count($catIds) > 0) {
				$catlist = array();
				foreach($catIds as $catId) {
					$catlist[] = " cid='{$catId}'";					
				}	
				$this->_query .= ' AND ('. implode(' OR ', $catlist).')';				
			}

			if ($search != '') {
				$fields = array('name', 'quicktake', 'description');

				$where = array();

				$search = $this->_db->getEscaped( $search, true );

				foreach ($fields as $field) {
					$where[] = $field . " LIKE '%{$search}%'";
				}

				$this->_query .= ' AND (' . implode(' OR ', $where).')';
			}
			
			if(count($idList) > 1) {
				$this->_query .= ' AND ('. implode(' OR ', $idList).')';
			}

			$this->_query .= GalleryHelper::getAccessLevelCC();
		}

		switch($display) {				
			case 'ordering':
				$this->_query .= " ORDER BY ordering ".$this->_sort_dir;
				break;
			case 'random':
				$this->_query .= " ORDER BY RAND()";
				break;
			case 'popular':
				$this->_query .= " ORDER BY #__xgallery.hits ".$this->_sort_dir;
				break;
			case 'latest':
			default:
				$this->_query .= " ORDER BY #__xgallery.creation_date ".$this->_sort_dir;	
				break;					
		}

		return $this->_query;
	}
	
	function getTotal() {
		if (!$this->_total) {
			$this->_total = 0;
		}
		return $this->_total;
	}
	
	function getPagination() {
		if(!$this->_pagination) {
			jimport('joomla.html.pagination');
			global $mainframe;
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}
	
	function getSearch()
	{
		if (!$this->_search) {
			global $mainframe, $option;

			$search = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
			$this->_search = JString::strtolower($search);
		}

		return $this->_search;
	}
}
?>

