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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

define('CURR_CONT', JRequest::getVar('currCont'));
define('CURR_ID', JRequest::getVar('id'));
define('CURR_TYPE', JRequest::getVar('reqType'));

class XGalleryControllerFolders extends JController {
	
	function __construct($config = array()) {
		parent::__construct($config);
	}
	
	function add() {
		$this->display();
	}
	
	function display() {			
		global $mainframe;
		$vName = JRequest::getCmd('view', 'media');
		
		switch ($vName) {
			case 'images':
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				$mName = 'manager';

				break;

			case 'imagesList':
				$mName = 'list';
				$vLayout = JRequest::getCmd( 'layout', 'default' );

				break;

			case 'mediaList':
				$mName = 'list';
				$vLayout = $mainframe->getUserStateFromRequest('xgallery.list.layout', 'layout', 'thumbs', 'word');
				//$vLayout = JRequest::getCmd( 'layout', 'default' );

				break;

			case 'media':
			default:
				$vName = 'media';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				$mName = 'manager';
				break;
		}
		
		$document = &JFactory::getDocument();
		$vType		= $document->getType();
			
		$view = &$this->getView($vName, $vType);
		
		// Get/Create the model
		if ($model = &$this->getModel($mName)) {

			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
		
		$view->setLayout($vLayout);
		$view->display();
		
	}
	
	function ftpValidate() {
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
	}
	
	function delete() {

		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$helper = new GalleryHelper();
		$cookieParams = $helper->getCookieParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		if($cfgParams->get('image_external')) {
			$base_folder_path = $cookieParams->bpath;
		} else {
			$base_folder_path = COM_MEDIA_BASE;
		}
				
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Get some data from the request
		$tmpl	= JRequest::getCmd( 'tmpl' );
		$paths	= JRequest::getVar( 'rm', array(), '', 'array' );
		$folder = JRequest::getVar( 'folder', '', '', 'path');

		// Initialize variables
		$msg = array();
		$ret = true;

		if (count($paths)) {
			foreach ($paths as $path)
			{
				if ($path !== JFile::makeSafe($path)) {
					JError::raiseWarning(100, JText::_('Unable to delete:').htmlspecialchars($path, ENT_COMPAT, 'UTF-8').' '.JText::_('WARNDIRNAME'));
					continue;
				}

				$fullPath = JPath::clean($base_folder_path.DS.$folder.DS.$path);
				if (is_file($fullPath)) {
					$ret |= !JFile::delete($fullPath);
				} else if (is_dir($fullPath)) {
					$files = JFolder::files($fullPath, '.', true);
					$canDelete = true;
					foreach ($files as $file) {
						if ($file != 'index.html') {
							$canDelete = false;
						}
					}
					if ($canDelete) {
						$ret |= !JFolder::delete($fullPath);
					} else {
						JError::raiseWarning(100, JText::_('Unable to delete:').$fullPath.' '.JText::_('Not Empty!'));
					}
				}
			}
		}
		if ($tmpl == 'component') {
			// We are inside the iframe
			$this->setRedirect('index.php?option=com_xgallery&task=add&controller=folders&view=mediaList&folder='.$folder.'&tmpl=component');
		} else {
			$this->setRedirect('index.php?option=com_xgallery&task=add&controller=folders&folder='.$folder);
		}
	}

	/**
	 * Create a folder
	 *
	 * @param string $path Path of the folder to create
	 * @since 1.5
	 */
	function create() {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$folder			= JRequest::getCmd( 'foldername', '');
		$folderCheck	= JRequest::getVar( 'foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent			= JRequest::getVar( 'folderbase', '', '', 'path' );

		JRequest::setVar('folder', $parent);

		if (($folderCheck !== null) && ($folder !== $folderCheck)) {
			$this->setRedirect('index.php?option=com_xgallery&task=add&controller=folders&folder='.$parent, JText::_('WARNDIRNAME'));
		}

		if (strlen($folder) > 0) {
			$path = JPath::clean(COM_MEDIA_BASE.DS.$parent.DS.$folder);
			if (!is_dir($path) && !is_file($path))
			{
				jimport('joomla.filesystem.*');
				JFolder::create($path);
				JFile::write($path.DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
			}
			JRequest::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}
		$this->setRedirect('index.php?option=com_xgallery&task=add&controller=folders&folder='.$parent);
	}
}