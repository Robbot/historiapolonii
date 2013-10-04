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
defined('_JEXEC') or die('Restricted access');
	
class TableCollection extends JTable {
	
	var $id = null;
	var $cid = null;
	var $name = null;
	var $folder = null;
	var $thumb = null;
	var $hits = null;
	var $banner = null;
	var $quicktake = null;
	var $description = null;
	var $access = null;
	var $groupname = null;
	var $creation_date = null;
	var $published = null;
	var $ordering = null;
	var $metakey = null;
	var $metadesc = null;
	var $metaauthor = null;
	var $metarobots = null;
	
	function __construct(&$db) {
		parent::__construct('#__xgallery', 'id', $db);
	}
	
	function bind($vars, $ignore = array()) {
		parent::bind($vars, $ignore);
		
		if(!$this->id) {
			$this->ordering = $this->getNextOrder();
		}
		return true;
	}
}

