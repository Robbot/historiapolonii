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

$file = '';
$width = '';
$height = '';
$os = strtoupper(substr(php_uname('s'), 0, 3));

$file = $_REQUEST['file'];
$tn = isset($_REQUEST['tn']) ? (bool)$_REQUEST['tn'] : (bool)0;
//$wme = isset($_GET['wme']) ? (bool) $_GET['wme'] : 0;
$cParams = getCookieParams();

$type = $cParams->type;

if($tn == 0) {
	$width = isset($cParams->w) ? $cParams->w : 0;
	$height = isset($cParams->h) ? $cParams->h : 0;
} else {
	if($type == 'emb') {
		$width = isset($cParams->max_w) ? $cParams->max_w : '';
		$height = isset($cParams->max_h) ? $cParams->max_h : '';
	} else {
		$width = 0;
		$height = 0;
	}
}

if(substr($file, 0, 1) != '/' || substr($file, 0, 1) != '\\') {
	if($os === 'WIN') {
		$file = '\\'.$file;
	} else {
		$file = '/'.$file;
	}
}

if($cParams->wme) {
	$wmpath = $cParams->wmp;
	$wm_h_position = $cParams->wmh;
	$wm_v_position = $cParams->wmv;
	$wm_op = $cParams->wmop;
	$wm_perc = $cParams->wmrsperc;
	$reproc = $cParams->reproc;
	if($wm_op > 100 || $wm_op < 0) {
		$wm_op = 100;
	}
} else {
	$wmpath = "";
	$wm_h_position = "";
	$wm_v_position = "";
	$wm_op = "";
	$wm_perc = "";
	$reproc = false;
}

generateImg($_REQUEST['file'], $width, $height, $cParams->wme, $wmpath, $wm_h_position, $wm_v_position, $wm_op, $cParams->bpath, (bool) $_REQUEST['tn'], $wm_perc, $type, $os, $reproc);

function generateImg($img, $width, $height, $wme, $watermark, $wm_h_position, $wm_v_position, $logo_op, $bpath, $tn, $wm_perc, $type, $os, $reproc) {
	

	$img = $bpath.$img;
	$watermark = $bpath."/".$watermark;
	
	if ($os === 'WIN') {
		$img = str_replace('/', '\\', $img);
		$img = str_replace('\\\\', '\\', $img);
		$watermark = str_replace('/', '\\', $watermark);
	}

	$path = pathinfo($img);
	$origSize = getimagesize($img);
	$edgePadding = 15;

	if($width > 0 && $height > 0) {
		$xratio = $width/($origSize[0]);
		$yratio = $height/($origSize[1]);
	} else {
		if($tn == 0) {
			$width = 0;
			$height = 0;
		} else {
			$width = $origSize[0];
			$height = $origSize[1];
		}
		
		$xratio = $width/($origSize[0]);
		$yratio = $height/($origSize[1]);
	}

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			Header("Content-type: image/jpeg");
			$image = imagecreatefromjpeg($img);
			break;
		case "gif":
			Header("Content-type: image/gif");
			$image = imagecreatefromgif($img);
			break;
		case "png":
			Header("Content-type: image/png");
			$image = imagecreatefrompng($img);
			break;
		default:
			break;			
	}
	
	if($xratio < 1 || $yratio < 1) {
		if($xratio < $yratio) {
			$resized = imagecreatetruecolor($width,floor(imagesy($image)*$xratio));
		} else {
			$resized = imagecreatetruecolor(floor(imagesx($image)*$yratio), $height);
		}
	} else {
		if ($type == 'emb') {
			if($origSize[0] > $width || $origSize[1] > $height) {
				$water_resize_factor = $width / $origSize[0];
				$new_watermarkwidth  = $watermark_width_orig * $water_resize_factor;
				$new_watermarkheight = $origSize[1] * $water_resize_factor;
			} else {			
				$new_watermarkwidth = $origSize[0]; 
				$new_watermarkheight = $origSize[1];
			}
			$resized = imagecreatetruecolor($new_watermarkwidth, $new_watermarkheight);
		} else {
			$resized = imagecreatetruecolor($width, $height);
		}
	}
	
	if($wme && $tn) {
		$image_new_width = imagesx($resized);
		$image_new_height = imagesy($resized);		 

		$watermarkobj = imagecreatefrompng($watermark);
		$watermark_width_orig = imagesx($watermarkobj);
		$watermark_height_orig = imagesy($watermarkobj);
		
		if($watermark_width_orig > ($image_new_width / 2) || $watermark_height_orig > ($image_new_height / 2)) {
			
			// some simple resize math
			$water_resize_factor = ($image_new_width / 2) / $watermark_width_orig;
			$new_watermarkwidth  = $watermark_width_orig * $water_resize_factor;
			$new_watermarkheight = $watermark_height_orig * $water_resize_factor;
			
			//the new watermark creation takes place starting from here			
			$new_watermark = imagecreatetruecolor($new_watermarkwidth , $new_watermarkheight);
			$black = imagecolorallocate($new_watermark, 0, 0, 0);
			imagecolortransparent($new_watermark, $black);
			
			// imagealphablending is important in order to keep our png image (the watewrmark) transparent
			imagealphablending($new_watermark , true);
			imagecopyresampled($new_watermark ,$watermarkobj, 0, 0, 0, 0, $new_watermarkwidth,$new_watermarkheight, $watermark_width_orig, $watermark_height_orig);
		
			// assign the new values to the old variables
			$watermark_width_orig  = $new_watermarkwidth;
			$watermark_height_orig = $new_watermarkheight;
			$watermarkobj = $new_watermark;			
		}
		
    	// where to place the watermark?
    	switch($wm_h_position){
    		// find the X coord for placement
    		case 'l':
    			$placementX = $edgePadding;
    			break;
    		case 'c':
    			$wmcenter = round($watermark_width_orig / 2);
    			$imgcenter = round($image_new_width / 2);
    			$placementX = $imgcenter - $wmcenter;
    			break;
    		case 'r':
    			$placementX = $image_new_width - $watermark_width_orig - $edgePadding;
    			break;
    	}

    	switch($wm_v_position){
    		// find the Y coord for placement
    		case 't':
    			$placementY = $edgePadding;
    			break;
    		case 'c':
    			$placementY =  round($image_new_height / 2);
    			break;
    		case 'b':
    			$placementY = $image_new_height - $edgePadding - $watermark_height_orig;
    			break;
    	}
    	
		
		imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($image),imagesy($image));
		imagecopymerge($resized, $watermarkobj, $placementX, $placementY, 0, 0, $watermark_width_orig, $watermark_height_orig, $logo_op);
		//imagecopyresized($resized, $source, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($image),imagesy($image));
	} else {
		imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($image),imagesy($image));	
		//imagecopyresized($resized, $source, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($image),imagesy($image));
	}
	
	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			imagejpeg($resized);
			break;
		case "gif":
			imagegif($resized);
			break;
		case "png":
			imagepng($resized);
			break;
		default:
			break;			
	}
	  
	imagedestroy($resized);
	
	if($wme) {
		imagedestroy($watermarkobj);
	}
}

function getCookieParams() {  		
	if(!isset($_COOKIE['xgallery_cookie'])) {
		$fileParams = setCookieParams();
	} else {
		$serialParams = $_COOKIE['xgallery_cookie'];
		$serialParams = base64_decode($serialParams);
		$serialParams = gzuncompress($serialParams);
		$fileParams = unserialize($serialParams);
		
		$fileParams->wme = (bool) $fileParams->wme;
		$fileParams->reproc = (bool) $fileParams->reproc;
	}
		
	return $fileParams;
}

function setCookieParams() {
	if(!defined('_JEXEC')) {
		define('_JEXEC', 1);
	}
	
	if(!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}	
	
	if(!defined('JPATH_BASE')) {
		$joompath = dirname(dirname(dirname(dirname(realpath(_FILE_))))) . DS;
		define('JPATH_BASE', $joompath);
	}	
	
	require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
	require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php');
	$mainframe =& JFactory::getApplication('site');
	$mainframe->initialise();

	$component = JComponentHelper::getComponent( 'com_xgallery' );
  	$cfgParams = new JParameter( $component->params );
  		
  	$expire = time()+60*60;
  	$path = '/';
  	
	if(method_exists($mainframe, 'getParams')) {
  		$params =& $mainframe->getParams();
  	}
  	
  	$width = $cfgParams->get('coll_width'); //$params->get('col_width', $cfgParams->get('coll_width'));
	$height = $cfgParams->get('coll_height'); //$params->get('col_height', $cfgParams->get('coll_height'));
  		
	if($cfgParams->get('image_external', 0)) {
  		$baseDir = $cfgParams->get('image_external_path', '');
  	} else {
  		if(!defined('COM_MEDIA_BASE')) {
  			$imagPath = $cfgParams->get('image_path', 'images/stories');
  			$baseDir = JPATH_ROOT.$imagPath;
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
  	$pObject->wme = (bool) $cfgParams->get('enable_watermark', 0);
  	$pObject->wmp = $cfgParams->get('watermark_path', '');
  	$pObject->wmh = $cfgParams->get('wm_h_position', 'r');
  	$pObject->wmv = $cfgParams->get('wm_v_position', 'b');
  	$pObject->wmop = $cfgParams->get('wm_opacity', 100);
  	$pObject->reproc = (bool) $cfgParams->get('resize_image_upload', 0);
  	$pObject->wmrsperc = $resize_percent;
  	$pObject->w = $width;
  	$pObject->h = $height;
  	$pObject->type = '';
  	$pObject->ds = DS;
  	
  	if(isset($params)) {
  		$display_menu = $params->get('coll_display_layout', '');
  		$display_conf = $cfgParams->get('cfg_display_layout', 'shadowbox');

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
 		
	setCookie('xgallery_cookie', $fileParams, $expire, $path);
	
	return $pObject;
}

function imageResize($width, $height, $scale) {
	$width = $width * $scale/100;
    $height = $height * $scale/100;

	return array(round($width), round($height));
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
?>
	
