<?php
/**
* @version		$Id: view.html.php 11236 2008-11-02 02:44:35Z ian $
* @package		Joomla
* @subpackage	Media
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Media component
 *
 * @static
 * @package		Joomla
 * @subpackage	Media
 * @since 1.0
 */
class XGalleryViewMedia extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		$savedForm = $this->get('saveForm');
		
		$config =& JComponentHelper::getParams('com_xgallery');

		$style = $mainframe->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

		$listStyle = "
			<ul id=\"submenu\">
				<li><a id=\"thumbs\" onclick=\"MediaManager.setViewType('thumbs', '".CURR_CONT."', '".CURR_ID."', '".CURR_TYPE."')\">".JText::_('Thumbnail View')."</a></li>
				<li><a id=\"details\" onclick=\"MediaManager.setViewType('details', '".CURR_CONT."', '".CURR_ID."', '".CURR_TYPE."')\">".JText::_('Detail View')."</a></li>
			</ul>
		";
		
		$document =& JFactory::getDocument();
		$document->setBuffer($listStyle, 'modules', 'submenu');

		JHTML::_('behavior.mootools');
		$document->addScript(JURI::base(true).'/components/com_xgallery/assets/mediamanager.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/assets/mediamanager.css');
		$surl = 'index.php?option=com_xgallery&currCont='.CURR_CONT.'&id='.CURR_ID.'&reqType='.CURR_TYPE.'&task=add&controller=folders&view=mediaList&tmpl=component';
		$document->addScriptDeclaration("
			window.addEvent('domready', function(){
				// Added to populate data on iframe load
				MediaManager.initialize('".$surl."');
				MediaManager.trace = 'start';
				document.updateUploader = function() { MediaManager.onloadframe(); };
				MediaManager.onloadframe();
			});
		");
				
		JHTML::_('behavior.modal');
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			document.preview = '';
		});");
		
		JHTML::script('mootree.js');
		JHTML::stylesheet('mootree.css');

		if ($config->get('enable_flash', 0)) {
			JHTML::_('behavior.uploader', 'file-upload', array('onAllComplete' => 'function(){ MediaManager.refreshFrame(); }'));
		}
		
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
		
		if(DS == '\\')	{
			$base = str_replace(DS,"\\\\",$base);
		}
		
		$js = "
			var basepath = '".$base."';
			var viewstyle = '".$style."';
		" ;
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');
		$this->assignRef('session', JFactory::getSession());
		$this->assignRef('config', $config);
		$this->assignRef('state', $this->get('state'));
		$this->assign('require_ftp', $ftp);
		$this->assign('folders_id', ' id="media-tree"');
		$this->assign('folders', $this->get('folderTree'));
		
		$request_type = JRequest::getVar('reqType', '');
		$curr_cont = JRequest::getVar('currCont', '');
		
		if($curr_cont == 'collections') {
			if($request_type == 'folder') {
				$h_message = JText::_('COLLECTION GFM FOLDER HELP MESSAGE');
			} else {
				$h_message = JText::_('COLLECTION GFM THUMBNAIL HELP MESSAGE');
			}
		} else {
			$h_message = JText::_('CATEGORY GFM THUMBNAIL HELP MESSAGE');
		}
		$this->assign('help_message', $h_message);

		// Set the toolbar
		$this->_setToolBar();

		parent::display($tpl);
		echo JHTML::_('behavior.keepalive');
	}

	function _setToolBar()
	{
		// Get the toolbar object instance
		$bar =& JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'XGALLERY FILE MANAGER' ), 'mediamanager.png');

		// Add a delete button
		/* Re add later
		$title = JText::_('Delete');
		$dhtml = "<a href=\"#\" onclick=\"MediaManager.submit('folder.delete')\" class=\"toolbar\">
					<span class=\"icon-32-delete\" title=\"$title\" type=\"Custom\"></span>
					$title</a>";
		$bar->appendButton( 'Custom', $dhtml, 'delete' );
		*/
		$title = JText::_('Cancel');
		if(CURR_CONT == 'collections') {
			$curr_view = 'collection';
		} else {
			$curr_view = 'single';
		}
		$dhtml = "<a href=\"index.php?option=com_xgallery&controller=".CURR_CONT."&view=".$curr_view."&task=edit&id=".CURR_ID."\" class=\"toolbar\">
					<span class=\"icon-32-cancel\" title=\"$title\" type=\"Custom\"></span>
					$title</a>";
		$bar->appendButton( 'Custom', $dhtml, 'cancel' );

		// Add a popup configuration button
		JToolBarHelper::help( 'screen.mediamanager' );
	}

	function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt = null;
		if (isset($folder['children']) && count($folder['children'])) {
			$tmp = $this->folders;
			$this->folders = $folder;
			$txt = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}
		return $txt;
	}
}
