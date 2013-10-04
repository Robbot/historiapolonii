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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the XGallery component
 *
 * @static
 * @package		Joomla
 * @subpackage	Folders
 * @since 1.0
 */
class XGalleryViewFolders extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

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
			document.preview = SqueezeBox;
		});");

		JHTML::script('mootree.js');
		JHTML::stylesheet('mootree.css');

		if ($config->get('enable_flash', 0)) {
			JHTML::_('behavior.uploader', 'file-upload', array('onAllComplete' => 'function(){ MediaManager.refreshFrame(); }'));
		}

		if(DS == '\\')
		{
			$base = str_replace(DS,"\\\\",COM_MEDIA_BASE);
		} else {
			$base = COM_MEDIA_BASE;
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
		$this->assign('id', JRequest::getVar('id'));
		$user =& JFactory::getUser();
		$this->assignRef('user', $user);

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
		JToolBarHelper::title( JText::_( 'Gallery File Manager' ), 'mediamanager.png');

		// Add a delete button
		/* Re add this later
		$title = JText::_('Delete');
		$dhtml = "<a href=\"#\" onclick=\"MediaManager.submit('delete')\" class=\"toolbar\">
					<span class=\"icon-32-delete\" title=\"$title\" type=\"Custom\"></span>
					$title</a>";
		$bar->appendButton( 'Custom', $dhtml, 'delete' );
		*/
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
