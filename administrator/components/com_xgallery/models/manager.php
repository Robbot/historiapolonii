<?php
/**
 * @version		$Id: manager.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class XGalleryModelManager extends JModel
{

	function getState($property = null)
	{
		static $set;

		if (!$set) {
			$folder = JRequest::getVar( 'folder');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}
		return parent::getState($property);
	}

	/**
	 * Image Manager Popup
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.5
	 */
	function getFolderList($base = null) {

		// Get some paths from the request
		if (empty($base)) {
			$helper = new GalleryHelper();
			$cookieParams = $helper->getCookieParams();

			// Initialize variables
			$component = JComponentHelper::getComponent( 'com_xgallery' );
  			$cfgParams = new JParameter( $component->params );
  		
			if($cfgParams->get('image_external')) {
				$base = $cookieParams->bpath;
			} else {
				$base = COM_MEDIA_BASE;
			}
		}

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		// Load appropriate language files
		$lang = & JFactory::getLanguage();
		$lang->load('', JPATH_ADMINISTRATOR);
		$lang->load(JRequest::getCmd( 'option' ), JPATH_ADMINISTRATOR);

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Insert Image'));

		// Build the array of select options for the folder list
		$options[] = JHTML::_('select.option', "","/");
		foreach ($folders as $folder) {
			$folder 	= str_replace($base, "", $folder);
			$value		= substr($folder, 1);
			$text	 	= str_replace(DS, "/", $folder);
			$options[] 	= JHTML::_('select.option', $value, $text);
		}

		// Sort the folder list array
		if (is_array($options)) {
			sort($options);
		}

		// Create the drop-down folder select list
		$list = JHTML::_('select.genericlist',  $options, 'folderlist', "class=\"inputbox\" size=\"1\" onchange=\"ImageManager.setFolder(this.options[this.selectedIndex].value, ".CURR_CONT.", ".CURR_ID.", ".CURRTYPE.")\" ", 'value', 'text', $base);
		return $list;
	}

	function getFolderTree($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$helper = new GalleryHelper();
			$cookieParams = $helper->getCookieParams();

			// Initialize variables
			$component = JComponentHelper::getComponent( 'com_xgallery' );
  			$cfgParams = new JParameter( $component->params );
  		
			if($cfgParams->get('image_external')) {
				$base = $cookieParams->bpath;
			} else {
				$base = COM_MEDIA_BASE;
			}
		}
		$mediaBase = str_replace(DS, '/', $base.'/');

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		$tree = array();
		foreach ($folders as $folder)
		{
			$folder		= str_replace(DS, '/', $folder);
			$name		= substr($folder, strrpos($folder, '/') + 1);
			$relative	= str_replace($mediaBase, '', $folder);
			$absolute	= $folder;
			$path		= explode('/', $relative);
			$node		= (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);

			$tmp = &$tree;
			for ($i=0,$n=count($path); $i<$n; $i++)
			{
				if (!isset($tmp['children'])) {
					$tmp['children'] = array();
				}
				if ($i == $n-1) {
					// We need to place the node
					$tmp['children'][$relative] = array('data' =>$node, 'children' => array());
					break;
				}
				if (array_key_exists($key = implode('/', array_slice($path, 0, $i+1)), $tmp['children'])) {
					$tmp = &$tmp['children'][$key];
				}
			}
		}
		$tree['data'] = (object) array('name' => JText::_('Media'), 'relative' => '', 'absolute' => $base);
		return $tree;
	}
	
	function getSaveForm() {
		$session =& JFactory::getSession();
		
		$currCont = JRequest::getVar('currCont');

		if($currCont == 'categories') {
			$row =& JTable::getInstance('CategoryTmp', 'Table');
		} else {
			$row =& JTable::getInstance('CollectionTmp', 'Table');
		}
		
		if (!$row->bind(JRequest::get('post'))) {
			return false;
		}

		$row->quicktake = JRequest::getVar('quicktake', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$row->description = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		if (!$row->creation_date) {
			$row->creation_date = date('Y-m-d H:i:s');
		}
		
		$row->creation_date_tmp = date('Y-m-d H:i:s');
		$checkDate = date('Y-m-d H:i:s', strtotime("-30 minutes"));
		
		if (!$row->store()) {
			return false;
		}
		
		if($currCont == 'categories') {
			$query = "DELETE FROM #__xgallery_categories_tmp WHERE `creation_date_tmp` < '{$checkDate}'";
			$session->set('id_cat_tmp', $row->id_tmp, 'xgallery');
		} else {
			$query = "DELETE FROM #__xgallery_tmp WHERE `creation_date_tmp` < '{$checkDate}'";
			$session->set('id_coll_tmp', $row->id_tmp, 'xgallery');
		}
		
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		return true;
	}
}