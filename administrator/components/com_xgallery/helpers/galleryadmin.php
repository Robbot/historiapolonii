<?php
/**
 * @version		$Id: galleryadmin.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	GalleryAdmin
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * @package		Joomla
 * @subpackage	GalleryAdmin
 */
class GalleryAdminHelper {
	
	function getNextCategoryOrder($pid) {
		$query = "SELECT MAX(ordering) FROM #__xgallery_categories WHERE `pid` = {$pid}";
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$row = $db->loadResult();

		return $row + 1;
	}

	function getNextCollectionOrder($cid) {
		$query = "SELECT MAX(ordering) FROM #__xgallery WHERE `cid` = {$cid}";
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$row = $db->loadResult();
		
		return $row + 1;
	}
}