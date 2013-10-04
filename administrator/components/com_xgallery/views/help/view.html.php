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
jimport( 'joomla.document.feed.renderer.rss' );

class XGalleryViewHelp extends JView {
	function display($tpl = null) {
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		$doc =& JFactory::getDocument();
		$doc->addStyleSheet(JURI::base(true).'/components/com_xgallery'.DS.'assets'.DS.'style.css');
		
		$data = $this->get('Data');
		$actualItems = count( $data->items );
		
		if(count($actualItems) == 0) { 
			$data->items = array();
		}
		
		$this->assign('feed', $data);		
		
		$setItems = $cfgParams->get('rssitems', 5);
	
		if ($setItems > $actualItems) {
			$totalItems = $actualItems;
		} else {
			$totalItems = $setItems;
		}
		
		$this->assign('totalItems', $totalItems);
	
		parent::display($tpl);
	}
}