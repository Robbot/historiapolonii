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
		
		$cookieParams = GalleryHelper::getCookieParams();
		
		//$cfgParams = &JComponentHelper::getParams( 'com_xgallery' );
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		$collections = $this->get('data');
		
		$id = JRequest::getInt('id', '');
		$document =& JFactory::getDocument();
				
		// set feed title
		$document->setTitle($cfgParams->get('rss_title'));
		$document->setLink(JRoute::_('index.php?option=com_xgallery&view=main'));
		
		foreach($collections as $collection) {
			// create feed item
			$item = new JFeedItem();

			$item->title = $collection->name;
			
			$link = JRoute::_("index.php?option=com_xgallery&controller=single&view=single&id={$collection->id}");
			$linkfull = htmlentities(JRoute::_(JURI::root().$link));
			$item->link = $linkfull;
			
			$imgsrc = "file=".DS.urlencode($collection->thumb)."&amp;w=".$cfgParams->get('cat_width')."&amp;h=".$cfgParams->get('cat_height')."&amp;tn=0";
			$img = '<img src="'.JURI::base(true).'/components/com_xgallery/helpers/img.php?'.$imgsrc.'" alt="'.htmlspecialchars($collection->name).'" />';
			$thumb = "<a href='{$linkfull}'>{$img}</a>";
			$item->description = $thumb.$collection->quicktake;
			$item->date = date('r', strtotime($collection->creation_date));
			$item->pubDate = time();

			$document->addItem($item);
		}
		
		
	}
}
