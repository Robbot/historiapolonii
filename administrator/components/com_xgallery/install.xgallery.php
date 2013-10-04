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
function com_install() {
	/*
	$parser		=& JFactory::getXMLParser('Simple');
	$xml		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_xgallery' . DS . 'xgallery.xml';

	$parser->loadFile( $xml );
	$doc		=& $parser->document;
	*/
	//$element	=& $doc->getElementByPath( 'version' );
	//$version	= $element->data();
	
	$db =& JFactory::getDBO();	
	$query = "SELECT * FROM `#__xgallery_categories` WHERE id='1' LIMIT 1";
	$db->setQuery($query);
	$db->query();

	if ($db->getNumRows() == 0) {
		$query = "INSERT INTO `#__xgallery_categories` (`id`, `pid`, `name`, `thumb`, `hits`, `banner`, `quicktake`, `description`, `access`, `groupname`, `creation_date`, `published`, `ordering`) 
  		VALUES (1, 0, 'Main', '', 0, NULL, '<p>This is the main menu.</p>', '<p>This is the main menu.</p>', 0, 'Public', '', 1, 0)";
		$db->setQuery($query);
		$db->query();
	}
	
	$query = "SELECT metakey, metadesc, metaauthor, metarobots FROM `#__xgallery_categories`";
	$db->setQuery($query);
		
	if(!$db->query()) {
		$query = "ALTER TABLE `#__xgallery_categories` ADD `metakey` TEXT NOT NULL AFTER `published`, ADD `metadesc` TEXT NOT NULL AFTER `metakey`, ADD `metaauthor` TEXT NOT NULL AFTER `metadesc`, ADD `metarobots` TEXT NOT NULL AFTER `metaauthor`" ;
		$db->setQuery($query);
		$db->query();
		
		$query = "ALTER TABLE `#__xgallery` ADD `metakey` TEXT NOT NULL AFTER `published`, ADD `metadesc` TEXT NOT NULL AFTER `metakey`, ADD `metaauthor` TEXT NOT NULL AFTER `metadesc`, ADD `metarobots` TEXT NOT NULL AFTER `metaauthor`" ;
		$db->setQuery($query);
		$db->query();
	}
	
	// Version 1.8.3 Update
	$query = "SELECT ordering FROM `#__xgallery`";
	$db->setQuery($query);
	
	if(!$db->query()) {
		$query = "ALTER TABLE `#__xgallery` ADD `ordering` int(11) AFTER `published`";
		$db->setQuery($query);
		$db->query();
		
		$query = "SELECT id FROM `#__xgallery`";
		$db->setQuery($query);
		$rows = $db->loadResultArray();
		
		foreach($rows as $row) {
			$query = "UPDATE `#__xgallery` SET `ordering` = {$row} WHERE `id` = {$row}";
			$db->setQuery($query);
			$db->query();
		}
		
		$query = "ALTER TABLE `#__xgallery_categories` ADD `ordering` int(11) AFTER `published`";
		$db->setQuery($query);
		$db->query();
		
		$query = "SELECT id FROM `#__xgallery_categories`";
		$db->setQuery($query);
		$rows = $db->loadResultArray();
		
		foreach($rows as $row) {
			$query = "UPDATE `#__xgallery_categories` SET `ordering` = {$row} WHERE `id` = {$row}";
			$db->setQuery($query);
			$db->query();
		}
	}
	
	
?>
  <div class="header">
  	Congratulations, XGallery is now installed!
  </div>
  <div style="padding-left:65px;">
  	Be sure to click on Parameters and save your configuration before using XGallery.<br/>
  	Please review the <a href="http://www.optikool.com/documentation/xgallery-component" target="_blank">Documentation</a> or <a href="http://www.optikool.com/documentation/xgallery-component/faq" target="_blank">FAQs</a> for XGallery or send me an email if any problems are found.  
  </div>

  <?php
}
