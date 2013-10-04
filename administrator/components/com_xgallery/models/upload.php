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
defined('_JEXEC') or die ('Restricted access');
jimport('joomla.application.component.model');
	
class XGalleryModelUpload extends JModel {
		
	function __construct() {
		parent::__construct();
	}
	
	function getData() {
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
  		if(isset($_GET['xgallery_u_cookie'])) {
  			$cParams = $this->getCookieParams($_GET['xgallery_u_cookie']);
  		} else {
  			$cParams = $this->getCookieParams($_COOKIE['xgallery_cookie']);  			
  		} 		
  		
  		if($cfgParams->get('image_external', 0)) {
  			$baseDir = $cfgParams->get('image_external_path', '');
  		} else {
  			if(!defined('COM_MEDIA_BASE')) {
  				$imagPath = $cfgParams->get('image_path', 'images/stories');
  				$baseDir = JPATH_ROOT.DS.$imagPath;
  			} else {
  				$baseDir = COM_MEDIA_BASE;
  			}
  		}
  		
  		$collectionId = JRequest::getCmd('id', '');
  		
		if(empty($collectionId)) {
  			$user =& JFactory::getUser();  		
  			$fileSaveLocation = $baseDir . DS . $user->username . DS . $cParams->folder;
  		} else {
  			$row =& JTable::getInstance('Collection', 'Table');
			$row->load($collectionId);
			$fileSaveLocation = $baseDir . DS . $row->folder;
  		}
  		
		if(!JFolder::exists($fileSaveLocation)) {
			if(!JFolder::create($fileSaveLocation, 0755, true)) {
				$json = array("error" => "true",
								"msg" => $e->getMessage);
				return json_encode($json);
			}
		}		
		
		$file = JRequest::getVar('Filedata', null, 'files', 'array');
		
		if($file['error'] != UPLOAD_ERR_OK) {
			$json = array("error" => "true",
							"msg" => JText::_("UPLOAD FILE ERROR"));
			return json_encode($json);
		}
		
		//Check for valid upload
		if(!is_uploaded_file($file['tmp_name'])) {
			$json = array("error" => "true",
							"msg" => JText::_("INVALID REQUEST"));
			return json_encode($json);
		}
		 
		//check for filesize
		$fileSize = $file['size'];
		$upload_maxsize = JComponentHelper::getParams('com_media')->get('upload_maxsize');
		
		if($fileSize > $upload_maxsize) {
			$json = array("error" => "true",
							"msg" => JText::_("FILE TO LARGE"));
		    return json_encode($json);
		}
		
		$tmp_name = $file['tmp_name'];
		$name = JFile::makeSafe($file['name']);
		
		if(GalleryHelper::isImage($tmp_name)) {
			if(JFile::upload($tmp_name, $fileSaveLocation . DS . $name)) {
				chmod($fileSaveLocation . DS . $name, 0755);
				if(is_file($fileSaveLocation . DS . $name)) {
					$json = array("error" => "false",
								"msg" => JText::_("FILE UPLOAD COMPLETE"),
								"name" => $name,
								"path" => $fileSaveLocation . DS . $name);
				} else {
					$json = array("error" => "true",
								"msg" => "Not a File",
								"name" => $name,
								"path" => $fileSaveLocation . DS . $name);	
				}
				return json_encode($json);
			} else {
				if(JFolder::exists($fileSaveLocation)) {
					if(!is_writable($fileSaveLocation)) {
						$msg = JText::_("GALLERY PATH NOT WRITABLE");
					} 
				} else {
					$msg = JText::_("GALLERY PATH NOT EXIST");
				}
				
				$json = array("error" => "true",
							"msg" => "{$msg}");
				return json_encode($json);
			}
		} else { 
			$json = array("error" => "true",
							"msg" => JText::_("IMAGE NOT VALID"));
			return json_encode($json);
		}		
	}
	
	function getCookieParams($serialParams) {  	
		$serialParams = base64_decode($serialParams);
		$serialParams = gzuncompress($serialParams);
		$serialParams = unserialize($serialParams);
		$fileParams = $serialParams;
		return $fileParams;
	}
	
}
?>

