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
	
	if($errorChecking) {
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
	}
	
	define('COM_MEDIA_BASE',    JPATH_ROOT.DS.$imagPath);
	define('COM_MEDIA_BASEURL', JURI::root().$imagPath);
	define('COM_MEDIA_BASEPATH', DS.$imagPath);
	define('COM_IMAGE_PATH', $imagPath);
	define('XGALLERY_COOKIE', 'xgallery_cookie');
	
	JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
	require_once( JPATH_COMPONENT.DS.'helpers'.DS.'gallery.php' );
		
	$controller = JRequest::getCmd('controller');

	$path = JPATH_COMPONENT.DS.'controllers';

	switch($controller) {
		case 'categories':			
			$controller = 'categories';
			break;		
		case 'collections':
			$controller = 'collections';
			break;	
		case 'single':
			$controller = 'single';
			break;
		case 'feed':
			$controller = 'feed';
			break;
		case 'error':
			$controller = 'error';
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