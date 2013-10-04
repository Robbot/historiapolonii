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
	
class XGalleryModelCategory extends JModel {
	var $_data = null;
	var $_pagination = null;
	var $_total = null;
	var $_query = null;
	var $_search = null;
	var $_id = null;
	var $_cat = null;
	var $_catids = null;
	var $_catname = null;
	var $_catinfo = null;
	var $_pcatinfo = null;
	var $_coll_sort_on = null;
	var $_coll_sort_dir = null;
	var $_itemid = null;
	var $_menu = null;
	var $_catslug = null;
	var $_collslug = null;
	var $_component_id = null;
	
	function __construct() {
		parent::__construct();
		
		global $mainframe, $option;
		$params =& $mainframe->getParams();
		$cfgParams = &JComponentHelper::getParams( 'com_xgallery' );
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $params->get('prm_cat_coll_limit', $cfgParams->get('cfg_cat_coll_limit', $mainframe->getCfg('list_limit'))), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->_coll_sort_on = $params->get('prm_cat_coll_sort_order', $cfgParams->get('cfg_cat_coll_sort_order', 'id'));
		$this->_coll_sort_dir = $params->get('prm_cat_coll_sort_order_dir', $cfgParams->get('cfg_cat_coll_sort_order_dir', 'asc'));
		
		//In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
		
	function &getData() {		
		global $option;
		if(empty($this->_data)) {			
			$this->_id = JRequest::getInt('id');
			
			if($this->_id == 1) {
				$compId = GalleryHelper::getComponentId();
				$link = GalleryHelper::getRoute("index.php?option={$option}&view=main&Itemid={$compId->id}");
				header('Location: '.$link);
			}
			$query = $this->buildSearch();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			if(empty($this->_data)) {
				$this->_data = array();
			}
		}
		
		if(!empty($this->_data)) {
			$this->_cat = JRequest::getInt('id');
			GalleryHelper::checkAccessLevel($this->_cat);
		}
		
		$this->_getCategoryInfo();
		$this->getBreadcrumbs();
		
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
	
	function getCatName($cat = '') {
		if ($cat == '') {
			$cat = $this->_cat;
		}

		$query = "SELECT id, name, pid FROM #__xgallery_categories WHERE published = '1' AND id ='{$cat}' LIMIT 1";
		$row = $this->_getList($query);

		return $row;
	}
	
	/**
	 * Function to add current id name to the breadcrumbs. This function also checks
	 * if the current category has a parent category and if so adds that too. 
	 * 
	 */		
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
		$catalias = GalleryHelper::getCategorySlug();		
		
		$currid = GalleryHelper::getComponentId();
		
		if($currid->id == $this->getComponentID()) {
			if(isset($main)) {
				unset($pathway->_pathway);
				$pathway->addItem($main[1], JRoute::_($main[2].'&amp;Itemid='.$main[0]));		
			} 
			
			$breadcrumbs = $pathway->getPathway();

			if(count($breadcrumbs) > 0) {
				$count = count($breadcrumbs) - 1;
	
				$currURI =& JURI::getInstance($breadcrumbs[$count]->link);
				$itemid = $currURI->getVar('Itemid');
			
				if(!$itemid) {
					if(isset($main)) {
						$itemid = $main[0];
					}
				}
	
				if(isset($this->_data[0]) && $this->_data[0]->cid != 1) {					
					$catName = $this->getCatName();
		
					if ($catName[0]->pid != 1) {
						$catPreName = $this->getCatName($catName[0]->pid);
						$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catPreName[0]->id.':'.$catalias.'&Itemid='.$itemid);
						$pathway->addItem($catPreName[0]->name, $link);
					}
	
					if(strtolower($catName[0]->name) != strtolower($pathway->_pathway[0]->name)) {
						$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catName[0]->id.':'.$catalias.'&Itemid='.$itemid);
						$pathway->addItem($catName[0]->name, $link);
					}
				}
	
			}
		} else {
			$itemid = JRequest::getInt('Itemid');

			if(isset($this->_data[0]) && $this->_data[0]->cid != 1) {					
				$catName = $this->getCatName(JRequest::getInt('id'));
		
				if ($catName[0]->pid != 1) {
					$catPreName = $this->getCatName($catName[0]->pid);
					$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catPreName[0]->id.':'.$catalias.'&Itemid='.$itemid);
					$pathway->addItem($catPreName[0]->name, $link);
				}
	
				if(strtolower($catName[0]->name) != strtolower($pathway->_pathway[0]->name)) {
					$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catName[0]->id.':'.$catalias.'&Itemid='.$itemid);
					$pathway->addItem($catName[0]->name, $link);
				}
			}
		}
	}
	
	/**
	 * Function to return information for the current category.
	 *
	 * @return _catinfo An array of field values for the current category id. 
	 */
	function _getCategoryInfo() {
		if(!$this->_catinfo) {
			$category =& JTable::getInstance('category', 'Table');
			$category->load($this->_id);
		
			GalleryHelper::checkAccessLevel( $category->id );
			GalleryHelper::checkAccessLevel( $category->pid );

			$this->_catinfo = $category;
		}
		
		return $this->_catinfo;
	}
	
	/**
	 * Function to return information for the primary category of the current category.
	 *
	 * @return _catinfo An array of field values for the current category id. 
	 */
	function _getPrimaryCatInfo() {
		if(!$this->_pcatinfo) {
			$category =& JTable::getInstance('category', 'Table');
			$category->load($this->_catinfo->pid);
			
			GalleryHelper::checkAccessLevel( $category->id );
			GalleryHelper::checkAccessLevel( $category->pid );
			
			$this->_pcatinfo = $category;
		}
		return $this->_pcatinfo;
	}
	
	function getCategory() {
		global $option, $mainframe;
		$row =& JTable::getInstance('category', 'Table');
		$limitStart = JRequest::getVar('limitstart', '');
		
		if(!$row->load( $this->_id )) {			
			$mainframe->redirect();
			JError::raiseError('404', JText::_('CATEGORY NOT FOUND'));
		}
		
		if($limitStart == '') {						
			$row->hits = $row->hits + 1;			
			$row->store();
		}
		
		$this->_catname = $row->name;
		
		return $row;
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
	
	function getSubCategories() {
		global $mainframe;
		$params =& $mainframe->getParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		$where = $this->_getMenuCategories();
		
		$query = 	"SELECT a.*, c.countid AS countid";
		$query .=	" FROM #__xgallery_categories AS a";
		$query .=	" JOIN (SELECT c.pid, count(*) AS countid";
		$query .=	" FROM #__xgallery_categories AS c";
		$query .=	" GROUP BY c.pid) AS c";
		$query .=	" ON a.pid = c.pid";
		$query .= " WHERE a.published = '1'";
		$catIds = $this->_getCategories();
		
		if(count($catIds) > 0) {
			$catlist = array();
			foreach($catIds as $catId) {
				$catlist[] = " a.pid='{$catId}'";					
			}

			$query .= ' AND ('. implode(' OR ', $catlist).')';		
		}
		
		if(count($where) > 0) {
			$query .= ' AND ('. implode(' OR ', $where).')';			
		} 
		
		$query .= " AND a.id != 1";
		
		$query .= GalleryHelper::getAccessLevel();
		
		if($params->get('prm_cat_sort_order', $cfgParams->get('cfg_cat_sort_order')) == 'ordering') {
			$query .= ' ORDER BY ordering '.$params->get('cfg_cat_sort_order_dir', $cfgParams->get('cfg_cat_sort_order_dir'));
		} else {
			$query .= " ORDER BY ".$params->get('prm_cat_sort_order', $cfgParams->get('cfg_cat_sort_order'))." ".$params->get('prm_cat_sort_order_dir', $cfgParams->get('cfg_cat_sort_order_dir'));
		}
		
		if($params->get('prm_cat_sub_limit', $cfgParams->get('cfg_cat_sub_limit')) != 0) {
			$query .= " LIMIT ".$params->get('prm_cat_sub_limit', $cfgParams->get('cfg_cat_sub_limit'));		
		}
		
		$rows = $this->_getList($query);
		
		if(count($rows) == 0) {
			$rows = array();
		} else {
			if($this->_coll_sort_on == 'ordering' && count($rows) > 1) {			
				// Order Categories to tree
				$tree = array();
				$rows = GalleryHelper::getCategoryTree($rows, $tree, JRequest::getInt('id'), -1);
			}
		}
		
		if(!$this->_catslug) {
			$this->_catslug = GalleryHelper::getCategorySlug();
		}
		
		$count = count($rows);	
		
		for($i = 0; $i < $count; $i++) {
			$rows[$i]->slug = $rows[$i]->id . ':' . $this->_catslug;								
		}
		
		return $rows;
	}
	
	function _getMenuCategories() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');

		$query = "SELECT id, link, parent FROM #__menu WHERE published = '1' AND componentid='{$com->id}'";	// AND sublevel != '0'	
		$links = $this->_getList($query);
		$where = array();
		
		if(count($links) > 0) {
			foreach($links as $link) {				
				//$currURI = JURI::getInstance($link->link);
				if($link->id == $this->_id) {					
					foreach($links as $link2) {
						if($link->id == $link2->parent) {
							$currURI = JURI::getInstance($link2->link);
							$where[] = "id = '".$currURI->getVar('id')."'";	
						}
					}
				}
			}
		}
		
		return $where;
	}
	
	function getMenuCategory() {
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
	
	function _getMenuItems() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');

		$query = "SELECT id, link, parent FROM #__menu WHERE published = '1' AND componentid='{$com->id}'";	// AND sublevel != '0'
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
	
	function buildSearch() {
		if (!$this->_query) {
			$search = $this->getSearch();			
			$catIds = $this->_getCategories();
			
			$this->_query =  "SELECT DISTINCT #__xgallery.id AS id, #__xgallery.cid AS cid, #__xgallery.name AS name,";
			$this->_query .= " #__xgallery.thumb AS thumb, #__xgallery.hits AS hits, #__xgallery.creation_date AS creation_date,";
			$this->_query .= " #__xgallery.folder AS folder, #__xgallery.quicktake AS quicktake, #__xgallery.description AS description,";
			$this->_query .= " #__xgallery.ordering AS ordering, #__xgallery.groupname AS groupname";
			$this->_query .= " FROM #__xgallery, #__xgallery_categories";
			$this->_query .= " WHERE #__xgallery.published = '1' AND #__xgallery_categories.published = '1'";
			
			/*$this->_query .= " AND #__xgallery.cid = #__xgallery_categories.id";			
			if ($this->_id != 1) {
				$this->_query .= " AND #__xgallery.cid ='{$this->_id}'";
			}*/
			
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
			$this->_query .= GalleryHelper::getAccessLevelCC();
			if($this->_coll_sort_on == 'ordering') {
				$this->_query .= " ORDER BY ordering {$this->_coll_sort_dir}";
			} else {
				$this->_query .=  " ORDER BY {$this->_coll_sort_on} {$this->_coll_sort_dir}";
			}
		}

		return $this->_query;
	}
	
	function getTotal() {
		if (!$this->_total) {
			$query = $this->buildSearch();
			$this->_total = $this->_getListCount($query);
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
	
	function _getComponentID() {
		$com = JComponentHelper::getComponent('com_xgallery');

		$db =& JFactory::getDBO();		
		$query = "SELECT id FROM #__menu WHERE published = '1' AND componentid='{$com->id}'";		
		$db->setQuery($query);
		$id = $db->loadResult();
		
		return $id;
	}
	
	function getComponentID() {
		return $this->_component_id;
	}
}
?>

