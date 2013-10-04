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
	
class XGalleryModelError extends JModel {
	var $_data = array();
	var $_error_num = null;
	var $_title = null;
	var $_mesg = null;
	var $_cat = 1;
		
	function __construct() {
		parent::__construct();
		
		global $mainframe, $option;
	}
		
	function &getData() {		
		$this->_error_num = JRequest::getVar('error_num', '');
		$this->_data['title'] = $this->_getTitle();
		$this->_data['mesg'] = $this->_getMessage();

		return $this->_data;		
	}
	
	function _getTitle() {
		switch($this->_error_num) {
			case 0:
				$this->_title = JTEXT::_('COLLECTION NOT FOUND');
				break;
			case 1:
				$this->_title = JTEXT::_('COLLECTIONS NOT FOUND');
				break;
			case 2:
				$this->_title = JTEXT::_('CATEGORY NOT FOUND');
				break;
			case 3:
				$this->_title = JTEXT::_('PERMISSION DENIED');
				break;
			case 4:
				$this->_title = JTEXT::_('PERMISSION DENIED');
				break;
			default:
				$this->_title = '';
				break;
		}
		return $this->_title;
	}
	
	function _getMessage() {
		switch($this->_error_num) {
			case 0:
				$this->_mesg = JTEXT::_('COLLECTION NOT FOUND MSG');
				break;
			case 1:
				$this->_mesg = JTEXT::_('COLLECTIONS NOT FOUND MSG');
				break;
			case 2:
				$this->_mesg = JTEXT::_('CATEGORY NOT FOUND MSG');
				break;
			case 3:
				$this->_mesg = JTEXT::_('PERMISSION DENIED CAT MSG');
				break;
			case 4:
				$this->_mesg = JTEXT::_('PERMISSION DENIED COL MSG');
				break;
			default:
				$this->_mesg = '';
				break;
		}
		return $this->_mesg;
	}
	
	function getBreadcrumbs() {
		jimport('joomla.environment.uri');
		global $mainframe;

		$menu = $this->_getMenuItems();
		$pathway = &$mainframe->getPathway();
		$breadcrumbs = $pathway->getPathway();
		$count = count($breadcrumbs) - 1;
				
		if(!empty($breadcrumbs)) {
			$currURI =& JURI::getInstance($breadcrumbs[$count]->link);
			$itemid = $currURI->getVar('Itemid');

			if($itemid != $menu) {
				$catName = $this->getCatName();
				$pathway->addItem($catName[0]->name, '');
			}
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
	
	function getCatName() {
		$query = "SELECT name FROM #__xgallery_categories WHERE published = '1' AND id ='{$this->_cat}' LIMIT 1";
		$row = $this->_getList($query);

		return $row;
	}
}
?>

