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

class XGalleryModelMain extends JModel {
	
	function getSearchPlugin() {
		$plugin = JPluginHelper::getPlugin("search", "xgallery");

		if(empty($plugin)) {
			$result = Array('ok' => false, 'mesg' => JText::_('SEARCH NOT INSTALLED'));
			return $result;
		} else {
			if(!JPluginHelper::isEnabled("search", "xgallery")) {
				$result = Array('ok' => false, 'mesg' => JText::_('SEARCH NOT ENABLED'));
				return $result;
			} else {
				$result = Array('ok' => true, 'mesg' => JText::_('SEARCH ENABLED'));
				return $result;
			}
		}
	}
	
	function getXGalleryFCE() {		
		$query = "SELECT * FROM #__modules WHERE module='mod_xgallery_fce'";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();

		if($num_rows > 0) {
			$result = Array('ok' => true, 'mesg' => JText::_('FCE INSTALLED'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('FCE NOT INSTALLED'));
			return $result;
		}
	}
	
	function getXGalleryJScroller() {		
		$query = "SELECT * FROM #__modules WHERE module='mod_xgallery_jscroller'";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		
		if($num_rows > 0) {
			$result = Array('ok' => true, 'mesg' => JText::_('JSCROLLER INSTALLED'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('JSCROLLER NOT INSTALLED'));
			return $result;
		}
	}
	
	function getXGalleryMenu() {		
		$query = "SELECT * FROM #__modules WHERE module='mod_xgallery_menu'";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();

		if($num_rows > 0) {
			$result = Array('ok' => true, 'mesg' => JText::_('MENU INSTALLED'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('MENU NOT INSTALLED'));
			return $result;
		}		
	}
	
	function getXGalleryCollection() {
		$query = "SELECT * FROM #__modules WHERE module='mod_xgallery_collection'";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();

		if($num_rows > 0) {
			$result = Array('ok' => true, 'mesg' => JText::_('COLLECTION INSTALLED'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('COLLECTION NOT INSTALLED'));
			return $result;
		}
	}
	
	function getShadowbox() {		
		$plugin = JPluginHelper::getPlugin("system", "shadowbox");
		
		if(!JPluginHelper::isEnabled("system", "shadowbox")) {
			$result = Array('ok' => false, 'mesg' => JText::_('SHADOWBOX NOT ENABLED'));
			return $result;
		} else {
			$result = Array('ok' => true, 'mesg' => JText::_('SHADOWBOX ENABLED'));
			return $result;
		}
	}
	
	function getGalleryLocation() {
		jimport('joomla.filesystem.file');
		$helper = new GalleryHelper();
		$cookieParams = $helper->getCookieParams();
		
		$config = JFactory::getConfig();
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		
		if($cfgParams->get('image_external')) {
			$bpath = $cookieParams->bpath;
		} else {
			$bpath = COM_MEDIA_BASE;
		}		

		if(!JFolder::exists($bpath)) {
			$result = Array('ok' => false, 'mesg' => JText::_('GALLERY PATH NOT EXIST'));
			return $result;
		}
		
		if(is_writable($bpath)) {
			$result = Array('ok' => true, 'mesg' => JText::_('GALLERY PATH WRITABLE'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('GALLERY PATH NOT WRITABLE'));
			return $result;
		}
	}	
	
	function getCategory() {
		$query = "SELECT * FROM #__xgallery_categories ORDER BY hits desc LIMIT 10";
		$cdata = $this->_getList($query);
				
		return $cdata;
	}
	
	function getViewedCollection() {
		$query = "SELECT * FROM #__xgallery ORDER BY hits desc LIMIT 10";
		$cdata = $this->_getList($query);
		
		return $cdata;
	}
	
	function getNewCollection() {
		$query = "SELECT * FROM #__xgallery ORDER BY creation_date desc LIMIT 10";
		$cdata = $this->_getList($query);
		
		return $cdata;
	}
	
	function getComponentInfo() {
		$info = array();
		//Retreive version from install file
		$parser		=& JFactory::getXMLParser('Simple');
		$xml		= JPATH_COMPONENT . DS . 'xgallery.xml';

		$parser->loadFile( $xml );
		$doc		=& $parser->document;
		
		$element	=& $doc->getElementByPath( 'author' );
		$info['author'] = $element->data();
		
		$element	=& $doc->getElementByPath( 'version' );
		$info['version'] = $element->data();
		
		$element	=& $doc->getElementByPath( 'copyright' );
		$info['copyright'] = $element->data();
		
		$element	=& $doc->getElementByPath( 'authorurl' );
		$info['authorurl'] = $element->data();
		
		$element	=& $doc->getElementByPath( 'license' );
		list($link, $gpl) = explode(' ', $element->data());
		$info['gpl'] = $gpl;
		$info['gpllink'] = $link;
		
		return $info;		
	}
}