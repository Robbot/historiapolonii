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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class XGalleryModelHelp extends JModel {
	var $_rssurl = "http://www.optikool.com/index.php?option=com_content&view=category&layout=blog&id=14&Itemid=87&format=feed&type=rss";

	function &getData() {

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] 		= $this->_rssurl;
		$options['cache_time'] = null;
		
		/*
		if ($params->get('cache')) {
			$options['cache_time']  = $params->get('cache_time', 15) ;
			$options['cache_time']	*= 60;
		} else {
			$options['cache_time'] = null;
		}
		*/
		
		$rssDoc =& JFactory::getXMLparser('RSS', $options);
		
		$feed = new stdclass();

		if ($rssDoc != false) {
			// channel header and link
			$feed->title = $rssDoc->get_title();
			$feed->link = $rssDoc->get_link();
			$feed->description = $rssDoc->get_description();

			// channel image if exists
			$feed->image->url = $rssDoc->get_image_url();
			$feed->image->title = $rssDoc->get_image_title();

			// items
			$items = $rssDoc->get_items();

			// feed elements
			$feed->items = array_slice($items, 0, 5);
			
			
		} else {
			$feed = false;
		}

		return $feed;
	}
}