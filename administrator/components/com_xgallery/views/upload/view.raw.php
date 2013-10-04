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
	
jimport('joomla.application.component.view');
	
class XGalleryViewUpload extends JView {
	function display($tpl = null) {
		global $mainframe;
		$row = $this->get('data');
	
		echo $row;		
	}
}