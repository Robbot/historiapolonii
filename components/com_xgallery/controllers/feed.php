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
jimport('joomla.application.component.controller');


class XGalleryControllerFeed extends JController {
	function display() {
		global $mainframe;
		//$document =& JFactory::getDocument();
		//$document->setType('feed');

		//$model = &$this->getModel('rss');
		$view = JRequest::getVar('view');
		//$view = &$this->getView('rss', 'feed');
		//$view->setModel($model, true);
		//$view->setLayout('rss');
		
		if(!$view) {
				JRequest::setVar('view', 'rss');
		}
		parent::display();
	}
}
?>