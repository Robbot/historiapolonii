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

class XGalleryModelRss extends JModel {
	var $_id = null;
	var $_cat_ids = null;
	var $_max_items = null;
	var $_query = null;
	var $_show_protected = null;
	
	function __construct() {
		parent::__construct();
		
		global $mainframe, $option;
		$cfgParams = &JComponentHelper::getParams( 'com_xgallery' );
		
		$this->_max_items = $cfgParams->get('rss_max_items');

		$this->_show_protected = $cfgParams->get('rss_show_protected', 0);
				
	}
	
	function &getData() {			
		global $option;
		$this->_id = JRequest::getInt('id', 1);
		
		if(empty($this->_data)) {
			if($this->_id == '') {
				$this->_query = $this->_buildSearch();
			} else {
				$this->_query = $this->buildSearchWithId();
			}
			
			$this->_data = $this->_getList($this->_query);
		}

		return $this->_data;
		
	}
	
	function _buildSearch() {
		global $mainframe;

		if(!$this->_cat_ids) {
			$this->_cat_ids = $this->_getCollCategories();
		}
		
		if (!$this->_query) {
			$this->_query =  "SELECT #__xgallery.id AS id, #__xgallery.cid AS cid, #__xgallery.name AS name,";
			$this->_query .= " #__xgallery.thumb AS thumb, #__xgallery.hits AS hits, #__xgallery.creation_date AS creation_date,";
			$this->_query .= " #__xgallery.folder AS folder, #__xgallery.quicktake AS quicktake, #__xgallery.description AS description,";
			$this->_query .= " #__xgallery.groupname AS groupname";
			$this->_query .= " FROM #__xgallery, #__xgallery_categories";
			$this->_query .= " WHERE #__xgallery.published = '1' AND #__xgallery_categories.published = '1'";
			$this->_query .= " AND #__xgallery.cid = #__xgallery_categories.id";		

			if(count($this->_cat_ids) > 0) {
				$catlist = array();
				foreach($this->_cat_ids as $catId) {
					$catlist[] = " cid='{$catId}'";					
				}	
				$this->_query .= ' AND ('. implode(' OR ', $catlist).')';				
			}
			
			$this->_query .= GalleryHelper::getAccessLevelC();
		}
		
		$this->_query .= " ORDER BY creation_date DESC";
		$this->_query .= " LIMIT {$this->_max_items}";
		
		
		return $this->_query;
	}
	
	function buildSearchWithId() {
		if (!$this->_query) {
			
			if(!$this->_cat_ids) {
				$this->_cat_ids = $this->_getCollCategories();
			}
			
			$this->_query =  "SELECT DISTINCT #__xgallery.id AS id, #__xgallery.cid AS cid, #__xgallery.name AS name,";
			$this->_query .= " #__xgallery.thumb AS thumb, #__xgallery.hits AS hits, #__xgallery.creation_date AS creation_date,";
			$this->_query .= " #__xgallery.folder AS folder, #__xgallery.quicktake AS quicktake, #__xgallery.description AS description,";
			$this->_query .= " #__xgallery.groupname AS groupname";
			$this->_query .= " FROM #__xgallery, #__xgallery_categories";
			$this->_query .= " WHERE #__xgallery.published = '1' AND #__xgallery_categories.published = '1'";
						
			if(count($this->_cat_ids) > 0) {
				$catlist = array();
				foreach($this->_cat_ids as $catId) {
					$catlist[] = " cid='{$catId}'";					
				}	
				$this->_query .= ' AND ('. implode(' OR ', $catlist).')';				
			}

		
			$this->_query .= GalleryHelper::getAccessLevelC();
			$this->_query .=  " ORDER BY creation_date DESC";
			$this->_query .= " LIMIT {$this->_max_items}";
		}

		return $this->_query;
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

		if(empty($this->_cat_ids)) {

			$catids[] = $this->_id;
			$query = "SELECT id FROM #__xgallery_categories WHERE published = '1' AND pid= {$this->_id}".$access;
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

		if(count($catids) > 0) {
			$this->_cat_ids = $catids;
		}
		
		return $this->_cat_ids;
	}
	
}