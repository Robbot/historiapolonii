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

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xgallery'.DS.'tables');
	
class XGalleryViewError extends JView {
	function display($tpl = null) {
		global $mainframe;
		$params =& $mainframe->getParams();
		
		$message =& $this->get('data');

		$this->get('Breadcrumbs');

		$menuid = $this->get('MenuCategory');
		$menu = &JSite::getMenu();
		$active	= $menu->getActive();
		$active->tree[] = $menuid;
			
		$document = &JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/style.css');

		$this->assignRef('message', $message);

		parent::display($tpl);
	}
}