<?php
/**
 * JFUploader 3.1 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2013 TinyWebGallery
 * written by Michael Dempfle
 *
 * @license GNU / GPL 
 *   
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

if ($task) {
    $act = $task;
}

switch ( $act ) {
	case "edit":
	case "edituser":
	case "newConfig":
		    TOOLBAR_jfuploader::_EDIT();
		break;
	case "config":
	     $database = JFactory::getDBO();
         $my = JFactory::getUser();
         if (checkAccess($database, $my->usertype, 'backend_access_upload' )) {
		  TOOLBAR_jfuploader::_LIST();	
		} else {
          TOOLBAR_jfuploader::_HELP();
        }
		break;
	case "user":
		TOOLBAR_jfuploader::_USER();	
		break;	
	case "upload":
		TOOLBAR_jfuploader::_UPLOAD();	
		break;
		case "plugins":
		TOOLBAR_jfuploader::_PLUGINS();	
		break;
	case "help":
		TOOLBAR_jfuploader::_HELP();	
		break;
	default:
		TOOLBAR_jfuploader::_DEFAULT();
		break;
}
?>