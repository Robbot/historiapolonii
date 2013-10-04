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
defined('_JEXEC') or die();

class JElementXGalleryHeading extends JElement {
	var	$_name = 'XGalleryHeading';

	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return '&nbsp;';
	}

	function fetchElement($name, $value, &$node, $control_name) {
		if ($value) {
			return '<div style="color: #080808; background: #abee13; padding:5px; font-weight:bold; display:block;">' . JText::_($value) . '</div>';
		} else {
			return '<hr />';
		}
	}
}

class JElementXGallerySubHeading extends JElement {
	var $_name = 'XGallerySubHeading';
	
	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return '&nbsp;';
	}

	function fetchElement($name, $value, &$node, $control_name) {
		if ($value) {
			return '<div style="color:#658817; background-color:#d7d6d6; padding:5px; display:block;">' . JText::_($value) . '</div>';
		} else {
			return '<hr />';
		}
	}
}
?>