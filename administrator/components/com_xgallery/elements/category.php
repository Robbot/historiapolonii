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

class JElementCategory extends JElement {
	function fetchElement($name, $value, &$node, $control_name) {
		$db =& JFactory::getDBO();
		
		$query = 'SELECT id, name FROM #__xgallery_categories WHERE published=1 AND id != 1 ORDER BY name';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		return JHTML::_('select.genericlist', $options, $control_name . '[' . $name . ']', 'class="inputbox"', 'id', 'name', $value, $control_name . $name);
	}
}