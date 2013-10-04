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
/**
 * @package		Joomla
 * @subpackage	Gallery
 */
class GalleryHelper {
	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	function isImage( $fileName ) {		
		list($width, $height, $type, $attr) = getimagesize($fileName);
		if ($type == IMAGETYPE_GIF) {
			return true;
		} elseif ($type == IMAGETYPE_JPEG) {
			return true;
		} elseif ($type == IMAGETYPE_PNG) {
			return true;
		} elseif ($type == IMAGETYPE_BMP) {
			return true;
		}			
		return false;
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	function getTypeIcon( $fileName ) {
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}
	
	function parseSize($size) {
		if ($size < 1024) {
			return $size . ' bytes';
		} else {
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	function imageResize($width, $height, $target) {
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	function countFiles( $dir ) {
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}
	
	function getAccessLevel() {
		$user =& JFactory::getUser();
		$type = '';
		switch($user->usertype) {
			case '':
				$type = " AND access='0'";
				break;
			case 'Registered':
				$type = " AND (access='0' OR access='1')";
				break;			
			default:
				$type = " AND (access='0' OR access='1' OR access='2')";
				break;
		}
		return $type;
	}
	
	function getAccessLevelC() {
		$user =& JFactory::getUser();
		$type = '';
		switch($user->usertype) {
			case '':
				$typeCat = " AND #__xgallery_categories.access='0'";
				$typeCol = " AND #__xgallery.access='0'";
				break;
			case 'Registered':
				$typeCat = " AND (#__xgallery_categories.access='0' OR #__xgallery_categories.access='1')";
				$typeCol = " AND (#__xgallery.access='0' OR #__xgallery.access='1')";
				break;			
			default:
				$typeCat = " AND (#__xgallery_categories.access='0' OR #__xgallery_categories.access='1' OR #__xgallery_categories.access='2')";
				$typeCol = " AND (#__xgallery.access='0' OR #__xgallery.access='1' OR #__xgallery.access='2')";
				break;
		}
		
		$type = $typeCol . $typeCat;
		return $type;
	}
	
	function getAccessLevelCC() {
		$user =& JFactory::getUser();
		$type = '';
		switch($user->usertype) {
			case '':
				$typeCat = " AND #__xgallery_categories.access='0'";
				$typeCol = " AND #__xgallery.access='0'";
				break;
			case 'Registered':
				$typeCat = " AND (#__xgallery_categories.access='0' OR #__xgallery_categories.access='1')";
				$typeCol = " AND (#__xgallery.access='0' OR #__xgallery.access='1')";
				break;			
			default:
				$typeCat = " AND (#__xgallery_categories.access='0' OR #__xgallery_categories.access='1' OR #__xgallery_categories.access='2')";
				$typeCol = " AND (#__xgallery.access='0' OR #__xgallery.access='1' OR #__xgallery.access='2')";
				break;
		}
		
		$type = $typeCol . $typeCat;
		return $type;
	}
	
	function checkAccessLevel( $cat ) {
		global $option;
		$user =& JFactory::getUser();
		$type = '';
		$hasPid = true;

		$query = "SELECT access, pid FROM #__xgallery_categories WHERE published = '1' AND id ='{$cat}' LIMIT 1";
		$row = $this->_getList($query);
		
		switch($user->usertype) {
			case '':
				$type = array('0');
				break;
			case 'Registered':
				$type = array('0', '1');
				break;			
			default:
				$type = array('0', '1', '2');
				break;
		}
		
		$myRow = null;
		foreach ($row as $r) {
			$myRow = $r;
		}

		do {
			if(!empty($row)) {
				if(!in_array($row[0]->access, $type)) {
					//JError::raiseError('404', JText::_('COLLECTION NOT FOUND'));
					$itemid = GalleryHelper::getXGalleryItemId();
					$slug = $cat . ":" . GalleryHelper::getCategorySlug($cat);					 
					$link = "index.php?option=com_xgallery&view=category&id={$slug}&Itemid={$itemid->id}";
					$redirectUrl = base64_encode($link);
					$redirectUrl = '&return='.$redirectUrl;	
					$joomlaLoginUrl = 'index.php?option=com_user&view=login';				
              		$finalUrl = $joomlaLoginUrl . $redirectUrl;
					
					header('Location: '.JRoute::_($finalUrl));
				}			
			
				$query = "SELECT access, pid FROM #__xgallery_categories WHERE published = '1' AND id ='{$row[0]->pid}' LIMIT 1";
				$row = array();
				$row = $this->_getList($query);
			
				if(!isset($row[0]->pid) || $row[0]->pid == 0) {
					$hasPid = false;
				}
			} else {
				$hasPid = false;
			}		
		
		} while ($hasPid);
	}
	
	function getCategoryAccess() {
		$user =& JFactory::getUser();
		$type = '';
		switch($user->usertype) {
			case '':
				$type = " AND access != '1' AND access != '2'";
				break;
			case 'Registered':
				$type = " AND access != '2'";
				break;			
			default:
				$type = "";
				break;
		}
		
		$query = "SELECT id FROM #__xgallery_categories WHERE published = '1'" . $type;
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$rows = $db->loadResultArray();
		
		$ids = array();
		
		foreach($rows as $row) {
			$ids[] = "cid = {$row}";
		}
		
		$query = ' AND ('. implode(' OR ', $ids).')';
		
		return $query;
	}
	
	function getBreadcrumbPath( $cat ) {
		$query = "SELECT id, pid, name, access FROM #__xgallery_categories WHERE published = '1' AND id ='{$cat}' LIMIT 1";
		$row = $this->_getList($query);
		$cats = array();
		$hasPid = true;
		
		if  (isset($row[0]->pid) && $row[0]->pid != 0) {
			$cats[] = $row[0];
		}
		
		do {
						
			$query = "SELECT id, pid, name, access FROM #__xgallery_categories WHERE published = '1' AND id ='{$row[0]->pid}' LIMIT 1";
			$row = array();
			$row = $this->_getList($query);
			
			if(!isset($row[0]->pid) || $row[0]->pid == 0) {
				$hasPid = false;
			} else {
				$cats[] = $row[0];
			}	
		
		} while ($hasPid);
		
		return $cats;
	}
	
	function getMenuCategories() {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$access = $this->getAccessLevel();
		$query = "SELECT link FROM #__menu WHERE published = '1' AND componentid='{$com->id}'".$access;
		$links = $this->_getList($query);

		$where = array();
		if(count($links) > 0) {
			$where[] = "cid = '1'";
			$cid = '';
			foreach($links as $link) {
				$currURI = JURI::getInstance($link->link);
				$cid = $currURI->getVar('id');
				if(!empty($cid)) {
					$where[] = "cid = '".$currURI->getVar('id')."'";
				}
			}
		}
		
		return $where;
	}
	
	function getXGalleryItemId() {
		$com = JComponentHelper::getComponent('com_xgallery');

		$query = "SELECT id FROM #__menu WHERE published = '1' AND componentid='{$com->id}' AND link LIKE '%main%' LIMIT 1";
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$row = $db->loadObject();
		
		if(!isset($row) || empty($row)) {
			$row = new stdClass;
		}		
		
		return $row;		
	}
	
	function getComponentId() {
		$com = JComponentHelper::getComponent('com_xgallery');
		$query = "SELECT id FROM #__menu WHERE published = '1' AND componentid='{$com->id}' LIMIT 1";
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$row = $db->loadObject();
		
		if(!isset($row) || empty($row)) {
			$row = new stdClass;
			$row->id = null;
		}
		
		$currentId = JRequest::getVar('Itemid', '');

		if(isset($row->id) && $currentId != $row->id) {
			$row->id = $currentId;
		}
		
		return $row;		
	}
	
	function getGalleryId($id) {
		jimport('joomla.environment.uri');
		$com = JComponentHelper::getComponent('com_xgallery');
		$query = "SELECT id, link FROM #__menu WHERE componentid='{$com->id}'";
		$db =& JFactory::getDBO();
		$db->setQuery($query);		
		$links = $db->loadObjectList();
		
		$main = new stdClass;
		$views = array();
		$itemid = '';
		
		if(count($links) > 0) {
			foreach($links as $link) {
				$currURI = JURI::getInstance($link->link);

				if($currURI->getVar('view') == 'category') {
					$itemid = $currURI->getVar('id');
					$views[$itemid] = new stdClass;
					$views[$itemid]->id = $link->id;
				} else if ($currURI->getVar('view') == 'main') {
					$main->id = $link->id;
				} else if ($currURI->getVar('view') != 'single') {
					$main->id = $link->id;
				}
			}
		}
		
		if(array_key_exists($id, $views)) {
			return $views[$id];
		} else {
			return $main;
		}
	}
	
	function getRoute( $link ) {
		$link = JRoute::_($link);
		return $link;
	}
	
	function getBrowserType() {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$browser = array();
		if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
    		$browser['browser_version'] = $matched[1];
    		$browser['browser'] = 'IE';
		} elseif (preg_match( '|Opera ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
    		$browser['browser_version'] = $matched[1];
    		$browser['browser'] = 'Opera';
		} elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
        	$browser['browser_version'] = $matched[1];
        	$browser['browser'] = 'Firefox';
		} elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
        	$browser['browser_version'] = $matched[1];
        	$browser['browser'] = 'Safari';
		} else {
        	// browser not recognized!
    		$browser['browser_version'] = 0;
    		$browser['browser'] = 'other';
		}
		
		return $browser;
	}
	
	function calDimenions($origWidth, $origHeight, $maxWidth, $maxHeight) {
    
    	$myDimenions = array();
    
    	if (empty($origHeight) || $origHeight == "") {
        	$origHeight = $maxHeight;
    	}
        
    	if (empty($origWidth) || $origWidth == "") {
        	$origWidth = $maxWidth;
    	}
        
    	$imgratio = $origWidth / $origHeight;
    	
    	if($imgratio > 1) {
    		$newwidth = $maxWidth;
    		$newheight = $maxWidth / $imgratio;
    	} else {
    		$newwidth = $maxWidth;
    		$newheight = $maxWidth * $imgratio;
    	}
    	    
    	$myDimensions['width'] = round($newwidth);
    	$myDimensions['height'] = round($newheight);
    	
   		return $myDimensions;
	}
	
	function getCategory($id) {
		$query = "SELECT cid FROM #__xgallery WHERE published = '1' AND id='{$id}' LIMIT 1";
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$row = $db->loadObject();
		return $row;
	}
	
	function getCategorySlug() {
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		$alias = $cfgParams->get('cat_alias_name', 'category');
		
		return $alias;
	}
	
	function getCollectionSlug() {
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		$alias = $cfgParams->get('coll_alias_name', 'collection');
		
		return $alias;
	}
	
	function getCookieParams() {  		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$enable_cache = ((bool)$cfgParams->get('xgallery_cache_cookie', 0)) ? true : false;
  		
		$fileParams = GalleryHelper::setCookieParams();
		
		return $fileParams;
	}
	
	function setCookieParams() {
		global $mainframe;

		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
  		$menuitemid = JRequest::getInt( 'Itemid' );
  		if ($menuitemid) {
    		$menu = JSite::getMenu();
    		$menuparams = $menu->getParams( $menuitemid );
    		//$params->merge( $menuparams );
  		}

  		if(method_exists($mainframe, 'getParams')) {
  			$params =& $mainframe->getParams();
  		}
  		
  		$expire = time()+60*60;
  		$path = '/';
  		//$params =& $mainframe->getParams();
  		$width = $cfgParams->get('coll_width'); //$params->get('col_width', $cfgParams->get('coll_width'));
		$height = $cfgParams->get('coll_height'); //$params->get('col_height', $cfgParams->get('coll_height'));
		
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
 		
  		if($cfgParams->get('watermark_resize_percent', 50) > 100 || $cfgParams->get('watermark_resize_percent', 50) < 0) {
  			$resize_percent = 50;
  		} else {
  			$resize_percent = $cfgParams->get('watermark_resize_percent', 50);
  		}
  		
  		
  		$pObject = (object) array();
  		$pObject->bpath = $baseDir;
  		$pObject->wme = $cfgParams->get('enable_watermark', 0);
  		$pObject->wmp = $cfgParams->get('watermark_path', '');
  		$pObject->wmh = $cfgParams->get('wm_h_position', 'r');
  		$pObject->wmv = $cfgParams->get('wm_v_position', 'b');
  		$pObject->wmop = $cfgParams->get('wm_opacity', 100);
  		$pObject->reproc = $cfgParams->get('resize_image_upload', 0);
  		$pObject->wmrsperc = $resize_percent;
  		$pObject->w = $width;
  		$pObject->h = $height;
  		$pObject->type = '';

  		if(isset($params)) {
  			$display_menu = $params->get('coll_display_layout', '');
  			$display_conf = $cfgParams->get('display_layout', 'shadowbox');

  			if($display_menu !== '') {
  				if($display_menu !== 'shadowbox') {
  					$pObject->max_w = $cfgParams->get('coll_width_max', 500);
  					$pObject->max_h = $cfgParams->get('coll_height_max', 500);
  					$pObject->type = 'emb';
  				}
  			} elseif($display_conf !== 'shadowbox') {
  				$pObject->max_w = $cfgParams->get('coll_width_max', 500);
  				$pObject->max_h = $cfgParams->get('coll_height_max', 500);
  				$pObject->type = 'emb';
  			}
  		} 

  		$fileParams = serialize($pObject);
  		$fileParams = gzcompress($fileParams);
  		$fileParams = base64_encode($fileParams);
  		
  		$base = JURI::base();
  		$u =& JURI::getInstance( $base );
  		
  		setCookie('xgallery_cookie', $fileParams, $expire, $path, $u->getHost());

  		return $pObject;
	}
	
	function setUCookieParams($folder_name = '') {
		global $mainframe;
		jimport( 'joomla.filesystem.file' );
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
  		$expire = time()+60*60;
  		$path = '/';
  		
  		$width = $cfgParams->get('coll_width');
		$height = $cfgParams->get('coll_height');
		
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
  		
  		$user =& JFactory::getUser();
  		  		  		
  		$pObject = (object) array();
  		//$pObject->bpath = $baseDir;
  		//$pObject->username = JFile::makeSafe(strtolower($user->username));
  		
		if(JRequest::getVar('id', '') == '') {
  			$folder = $user->username . time(); 
  		} else {
  			$folder = $folder_name;
  		}
  		
  		$pObject->folder = $folder;   		
  		//$pObject->ds = DS;
  		//$pObject->upload_maxsize = JComponentHelper::getParams('com_media')->get('upload_maxsize');

  		$fileParams = serialize($pObject);
  		$fileParams = gzcompress($fileParams);
  		$fileParams = base64_encode($fileParams);
  		
  		$base = JURI::base();
  		$u =& JURI::getInstance( $base );
  		
  		setCookie('xgallery_u_cookie', $fileParams, $expire, $path, $u->getHost());
  		
  		return $pObject;
	}
	
	function getLoginRedirect($url) {
			$redirectUrl = base64_encode($url);			
			$joomlaLoginUrl = 'index.php?option=com_user&view=login';
			$finalUrl = $joomlaLoginUrl . $redirecturl;
			return $finalUrl;			
	}
	
	function getCategoryTree($data, $tree, $id = 0, $currentId) {		
		
		// Ordering
		$countItemsInCat = 0;
		foreach ($data as $key) {
			static $iCT = 0;// All displayed items
	
			if ($key->pid == $id && $currentId != $id && $currentId != $key->id ) {	
							
				$tree[$iCT] 					= new JObject();
				
				
				// Ordering MUST be solved here
				if ($countItemsInCat > 0) {
					$tree[$iCT]->orderup				= 1;
				} else {
					$tree[$iCT]->orderup 				= 0;
				}
				
				if ($countItemsInCat < ($key->countid - 1)) {
					$tree[$iCT]->orderdown 				= 1;
				} else {
					$tree[$iCT]->orderdown 				= 0;
				}
								
				$tree[$iCT]->id 				= $key->id;
				$tree[$iCT]->pid				= $key->pid;
				$tree[$iCT]->name 				= $key->name;
				$tree[$iCT]->thumb				= $key->thumb;
				$tree[$iCT]->hits				= $key->hits;
				$tree[$iCT]->banner				= $key->banner;
				$tree[$iCT]->quicktake			= $key->quicktake;
				$tree[$iCT]->description		= $key->description;
				$tree[$iCT]->access				= $key->access;
				$tree[$iCT]->groupname			= $key->groupname;
				$tree[$iCT]->creation_date		= $key->creation_date;
				$tree[$iCT]->published			= $key->published;
				$tree[$iCT]->ordering			= $key->ordering;
				$tree[$iCT]->metakey			= $key->metakey;
				$tree[$iCT]->metadesc			= $key->metadesc;
				$tree[$iCT]->metaauthor			= $key->metaauthor;
				$tree[$iCT]->metarobots			= $key->metarobots;
				
				$iCT++;
				
				$tree = GalleryHelper::getCategoryTree($data, $tree, $key->id, $currentId );
				$countItemsInCat++;
			} 
		} 
		
		return($tree);
	}
}
