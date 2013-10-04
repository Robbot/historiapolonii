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

	$component = JComponentHelper::getComponent( 'com_xgallery' );
  	$cfgParams = new JParameter( $component->params );
	$imagPath = $cfgParams->get('image_path', 'images/stories');
	$errorChecking = $cfgParams->get('enable_error', 0);
	
	$imagPath = str_replace('/', DS, $imagPath);
	
	if($errorChecking) {
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
	}

	define('COM_MEDIA_BASE',    JPATH_ROOT.DS.$imagPath);
	define('COM_MEDIA_BASEURL', JURI::root().$imagPath);
	define('COM_MEDIA_BASEPATH', DS.$imagPath);

	JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
	require_once( JPATH_COMPONENT_SITE.DS.'helpers'.DS.'gallery.php' );
	require_once( JPATH_COMPONENT.DS.'helpers'.DS.'media.php' );
	require_once( JPATH_COMPONENT.DS.'helpers'.DS.'galleryadmin.php' );
		
	$controller = JRequest::getCmd('controller');
	$path = JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers';

	switch($controller) {
		case 'categories':			
			$controller = 'categories';
			break;
		case 'collections':
			$controller = 'collections';
			break;
		case 'upload':
			$controller = 'upload';
			break;
		case 'file':
			$controller = 'file';
			break;
		case 'help':
			$controller = 'help';
			break;
		case 'folders':
			$controller = "folders";
			$cmd = JRequest::getCmd('task', null);

			if (strpos($cmd, '.') != false) {
				// We have a defined controller/task pair -- lets split them out
				list($controller, $task) = explode('.', $cmd);

				// Define the controller name and path
				$controller	= strtolower($controller);

				// If the controller file path exists, include it ... else lets die with a 500 error
				if (!file_exists($path.DS.$controller.'.php')) {				
					JError::raiseError(500, 'Invalid Controller');
				}
			} else {
				// Base controller, just set the task :)				
				$task = $cmd;
			}
			break;
		case 'main':
		default:
			$controller = 'main';
			break;				
	}
	
	require_once($path.DS.$controller.'.php');
	$controllerName = 'XGalleryController'.$controller;
	$controller = new $controllerName();
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
?>