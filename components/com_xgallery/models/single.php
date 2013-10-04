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
	
class XGalleryModelSingle extends JModel {
	var $_data = null;
	var $_pagination = null;
	var $_total = null;
	var $_query = null;
	var $_search = null;
	var $_cat = null;
	var $_start = null;
	var $_limit = null;
	var $_limitStart = null;
	var $_component_id = null;
	var $_breadcrumbs = null;
	var $_params = null;
	var $_cfgParams = null;
		
	function &getData() {		
		global $option;
		
		if(empty($this->_data)) {			
			$query = $this->buildSearch();
			$this->_data = $this->_getList($query);

			if(empty($this->_data)) {
				JError::raiseError('404', JText::_('COLLECTION NOT FOUND'));;
			}
		}
		
		$this->_cat = $this->_data[0]->cid;
		GalleryHelper::checkAccessLevel($this->_cat);
		$this->_breadcrumbs = GalleryHelper::getBreadcrumbPath($this->_cat);
		$this->_component_id = $this->_getComponentID();
		
		return $this->_data;
	}
	
	function getImages() {
		global $mainframe;
		$helper = new GalleryHelper();
		$cookieParams = $helper->getCookieParams();
		
		if(!$this->_params) {
			$this->_params =& $mainframe->getParams();
		}
		
		$config = JFactory::getConfig();		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		
		if($cfgParams->get('image_external')) {
			$bpath = $cookieParams->bpath;
		} else {
			$bpath = COM_MEDIA_BASE;
		}
		
		$path = $bpath.DS.$this->_data[0]->folder.DS;
		$iterator = new DirectoryIterator($path);
		$dirfiles = array();
		$displayImages = array();
		$id = JRequest::getInt('id');
		$this->_limitStart = JRequest::getVar('limitstart', 0);
		
		if($this->_limitStart == 0) {
			$row =& JTable::getInstance('collection', 'Table');
			$row->load( $id );			
			$row->hit( $id );			
		}
		
		foreach($iterator as $file) {
			if(!$file->isDot() && $file->isFile()) {
				$tempPath = $bpath.DS.$this->_data[0]->folder.DS.$file->getFilename();
				if($helper->isImage($tempPath)) {
					$dirfiles[] = array('path' => DS.$this->_data[0]->folder.'/', 'name' => $file->getFilename());
				}
			}
		}
		//$config->getValue('config.list_limit', 0)		
		sort($dirfiles);

		$this->setState('limit', $mainframe->getUserStateFromRequest('com_xgallery.limit', 'limit', $this->_params->get('col_img_per_page', $cfgParams->get('img_per_page', 20)), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
		
		$this->_total = count($dirfiles);
		$this->_start = (int)JRequest::getVar('start');
		
		if($this->_start == '') {
			$this->_start = $this->_limitStart;
		}
		//$this->_limit = (int)JRequest::getVar('limit', $mainframe->getCfg('list_limit', 20));
		$pagination = $this->getPagination();	
		$this->_limit = $pagination->limit;
		
		for ($count = $this->_start; $count < $this->_start + $pagination->limit; $count++) {
			if($count < $this->_total) {
				$displayImages[] = $dirfiles[$count];
			}
		}
			
		return $displayImages;
	}
	
	function getCatName() {
		$query = "SELECT id, name, metakey, metadesc FROM #__xgallery_categories WHERE published = '1' AND id ='{$this->_cat}' LIMIT 1";
		$row = $this->_getList($query);

		return $row;
	}
	
	function getBreadcrumbs() {
		global $mainframe;
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$db =& JFactory::getDBO();		
		$query = "SELECT id, name, link FROM #__menu WHERE published = '1' AND componentid='{$com->id}' AND link LIKE '%main%'";		
		$db->setQuery($query);
		$main = $db->loadRow();
		$catalias = GalleryHelper::getCategorySlug();
		
		$menu = $this->_getMenuItems();
		$pathway = &$mainframe->getPathway();
		
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
					$itemid = $main[0];
				}

				if($this->_data[0]->cid != 1) {
					$catName = $this->getCatName();
					$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catName[0]->id.':'.$catalias.'&Itemid='.$itemid);
					$pathway->addItem($catName[0]->name, $link);
				}

				$pathway->addItem($this->_data[0]->name, '');
			}
		} else {
			$itemid = JRequest::getInt('Itemid');
			
			if($this->_data[0]->cid != 1) {
				$catName = $this->getCatName();
				$link = JRoute::_('index.php?option=com_xgallery&view=category&id='.$catName[0]->id.':'.$catalias.'&Itemid='.$itemid);
				$pathway->addItem($catName[0]->name, $link);
			}

			$pathway->addItem($this->_data[0]->name, '');
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
	
	function _getComponentID() {
		$com = JComponentHelper::getComponent('com_xgallery');

		$db =& JFactory::getDBO();		
		$query = "SELECT id FROM #__menu WHERE published = '1' AND componentid='{$com->id}' AND `link` LIKE '%main'";		
		$db->setQuery($query);
		$id = $db->loadResult();
		
		return $id;
	}
	
	function getComponentID() {
		return $this->_component_id;
	}
	
	function getMenuCategories() {
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
		
	function buildSearch() {
		global $mainframe;
		
		if(!$this->_params) {
			$this->_params =& $mainframe->getParams();
		}
		
		if (!$this->_query) {
			$search = $this->getSearch();
			$id = $this->_params->get('id', JRequest::getInt('id'));
			
			$this->_query =  "SELECT #__xgallery.id AS id, #__xgallery.cid AS cid, #__xgallery.name AS name,";
			$this->_query .= " #__xgallery.thumb AS thumb, #__xgallery.hits AS hits, #__xgallery.creation_date AS creation_date,";
			$this->_query .= " #__xgallery.folder AS folder, #__xgallery.quicktake AS quicktake, #__xgallery.description AS description,";
			$this->_query .= " #__xgallery.groupname AS groupname, #__xgallery.metakey AS metakey, #__xgallery.metadesc AS metadesc,";
			$this->_query .= " #__xgallery.metaauthor AS metaauthor, #__xgallery.metarobots AS metarobots";
			$this->_query .= " FROM #__xgallery, #__xgallery_categories";
			$this->_query .= " WHERE #__xgallery.published = '1' AND #__xgallery_categories.published = '1'";
			$this->_query .= " AND #__xgallery.cid = #__xgallery_categories.id";
			$this->_query .= " AND #__xgallery.id ='{$id}'";
			
			if ($search != '') {
				$fields = array('name', 'quicktake', 'description');

				$where = array();

				$search = $this->_db->getEscaped( $search, true );

				foreach ($fields as $field) {
					$where[] = $field . " LIKE '%{$search}%'";
				}

				$this->_query .= ' WHERE (' . implode(' OR ', $where).')';
			}

			$this->_query .=  GalleryHelper::getAccessLevelCC();
			$this->_query .=  " ORDER BY creation_date DESC";
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
	
	function &getPagination() {
		if(!$this->_pagination) {
			jimport('joomla.html.pagination');
			if(!$this->_params) {
				$this->_params =& $mainframe->getParams();
			}
			$component = JComponentHelper::getComponent( 'com_xgallery' );
  			$cfgParams = new JParameter( $component->params );
			global $mainframe;
			$this->_pagination = new JPagination($this->_total, JRequest::getVar('limitstart', 0), JRequest::getVar('limit', $this->_params->get('col_img_per_page', $cfgParams->get('img_per_page', 20))));
		}
		
		return $this->_pagination;
	}
	
	function getLimitStart() {
		if($this->_limitStart == '') {
			$ls = 0;
		} else {
			$ls = $this->_limitStart;
		}
		$limitstart = array('limit' => $this->_limit, 'start' => $ls);

		return $limitstart;
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

