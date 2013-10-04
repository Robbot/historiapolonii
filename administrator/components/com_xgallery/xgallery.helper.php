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

function fetchSelectList($id = null, $selected = null, $idType = 'pid') {
	$db =& JFactory::getDBO();
		
	if($id == null) {
		$query = 'SELECT id, name FROM #__xgallery_categories';
	} else {
		$query = 'SELECT id, name FROM #__xgallery_categories WHERE id != '.$id;
	}
		
	$db->setQuery($query);
	$ids = $db->loadObjectList();
		
	return JHTML::_('select.genericlist', $ids, $idType, 'class="inputbox"', 'id', 'name', $selected);				
}

