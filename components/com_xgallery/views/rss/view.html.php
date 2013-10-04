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
	
class XGalleryViewRss extends JView {
	function display($tpl = null) {
		global $mainframe;
		
		//$cfgParams = &JComponentHelper::getParams( 'com_xgallery' );
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		$collections =& $this->get('data');
		
		$id = JRequest::getInt('id', '');
		$document =& JFactory::getDocument();
				
		// set feed title
		$document->setTitle($cfgParams->get('rss_title'));
		$document->setLink(JRoute::_('index.php?option=com_xgallery&view=main'));
		
		$this->assignRef('collections', $collections);
				
		parent::display($tpl);
	}
}
