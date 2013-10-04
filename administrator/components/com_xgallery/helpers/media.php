<?php
/**
 * @version		$Id: media.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Media
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * @package		Joomla
 * @subpackage	Media
 */
class MediaHelper {
	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return boolean
	 */
	function isImage( $fileName ) {
		static $imageTypes = 'xcf|odg|gif|jpg|png|bmp';
		return preg_match("/$imageTypes/i",$fileName);
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
		}
		else
		{
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
	
	function uploadFile($max, $dir_local, $temp_dir, $file_types, $folder_exists){
        jimport('joomla.filesystem.file');
        jimport( 'joomla.filesystem.archive' );
        
        $component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$enable_resize = $cfgParams->get('resize_image_upload', 1);
		$info = array();
		$info['new_dir'] = '';
		$type = '';

		set_time_limit((int)$cfgParams->get('execution_time', 60));
		//Retrieve file details from uploaded file, sent from upload form
        $file = JRequest::getVar('file_upload', null, 'files', 'array'); 
        // Retorna: Array ( [name] => mod_simpleupload_1.2.1.zip [type] => application/zip 
        // [tmp_name] => /tmp/phpo3VG9F [error] => 0 [size] => 4463 ) 
 		
        // Check if file exists and if there's no errors
        if(isset($file) && $file['error'] != 4){ 
        	//Clean up filename to get rid of strange characters like spaces etc
        	$filename = JFile::makeSafe($file['name']);

			if($file['error'] == 1) {
				$info['error'] = true;
				$info['msg'] = JText::_('ONLY FILES UNDER').' '.$max;
				return $info;
			}
            //Set up the source and destination of the file
 			$ext = JFile::getExt(strtolower($file['name']));
            $src = $file['tmp_name'];
            $dest = $temp_dir . DS . $filename;
            $file_info = pathinfo($file['name']);
            $directory_name = JFile::makeSafe($file_info['filename']);

            if (in_array($file['type'], $file_types)) { 
            	if ( JFile::upload($src, $dest) ) { 
            		//Redirect to a page of your choice
            		switch($file['type']) {
            			case "image/png":
            			case "image/gif":
            			case "image/jpeg":
            				$type = 'img';
            				$error = MediaHelper::createDirectory($dir_local, $directory_name, $type, $folder_exists);
            				
            				if ($error['error']) {
            					$info['error'] = true;
            					$info['msg'] = $error['msg'];
            				} else {
            					if(MediaHelper::moveFile($filename, $dest, $error['new_path'])) {
            						$info['new_dir'] = $error['new_path'];
            						$info['error'] = false;
            						$info['msg'] = JText::_('UPLOAD SUCCESSFUL');            						
            					} else {
            						$info['error'] = true;
            						$info['msg'] = JText::_('ERROR MOVE FILE');
            					}         					
            				}
         				
            				break;
            			case "application/zip":
            			case "application/x-zip-compressed":
            			case "application/x-gzip":
            				$type = 'zip';
            				//echo "dir_local: {$dir_local}<br/>";
            				//echo "directory_name: {$directory_name}<br/>";
            				//echo "type: {$type}<br/>";
            				//echo "folder_exits: {$folder_exists}<br/>";
            				$error = MediaHelper::createDirectory($dir_local, $directory_name, $type, $folder_exists);
            				
            				if(!$error['error']) {
            					if(JArchive::extract( $dest, $temp_dir .DS. $file_info['filename'] )) {
            						//echo "Moving extracted files<br/>";
            						//echo "From: ".$temp_dir .DS. $file_info['filename']."<br/>";
            						//echo "To: ".$error['new_path']."<br/>";
            						
            						if(MediaHelper::moveFiles($temp_dir .DS. $file_info['filename'], $error['new_path'])) {
            							JFolder::delete($temp_dir .DS. $file_info['filename']);
            							JFile::delete($dest);

            							$info['new_dir'] = $directory_name;
            							$info['error'] = false;
            							$info['msg'] = JText::_('UPLOAD SUCCESSFUL');            						
            						} else {
            							$info['error'] = true;
            							$info['msg'] = JText::_('ERROR MOVE FILE');
            						}
            						
            					} else {
            						$info['error'] = true;
            						$info['msg'] = JText::_('ERROR IN EXTRACT FILES');
            					}
            				} else {
            					$info['error'] = true;
            					$info['msg'] = $error['msg'];
            				}
            				break; 
            		}					
				} else {
					//Redirect and throw an error message
					$info['error'] = true;
					$info['msg'] = JText::_('ERROR IN UPLOAD');
				}
			} else {
				//Redirect and notify user file is not right extension
				$info['error'] = true;
				$info['msg'] = JText::_('FILE TYPE INVALID');
			}
        }
        return $info;
	}

	function createDirectory($dir_local, $directory_name, $type, $folder_exists) {
		jimport('joomla.filesystem.file');
		
		$path_names = explode(DS, $dir_local);
		$count = count($path_names);
		$error = array();
		$error['error'] = false;
		$error['msg'] = '';
		$name = MediaHelper::removeWhiteSpace($directory_name);
		
		if($count > 0) {
			$count--;
		}

		if($path_names[$count] != $name && $type != 'img' && !$folder_exists) {
			if(!JFolder::create($dir_local .DS. strtolower($name), 0755)) {
				$error['error'] = true;
				$error['msg'] = JText::_('ERROR IN CREATE DIRECTORY');
			} else {
				$error['error'] = false;
				$error['new_dir'] = strtolower($name);
				$error['new_path'] = $dir_local .DS. strtolower($name);
			}
		} else {
			$error['error'] = false;
			$error['new_path'] = $dir_local;
		}
		
		return $error;	
	}
	
	function moveFile($name, $src, $path) {
		jimport('joomla.filesystem.file');
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$enable_resize = (bool)$cfgParams->get('resize_image_upload', 1);
		$name = MediaHelper::removeWhiteSpace($name);
		
		if(is_writable($path)) {
			if($enable_resize) {
				$check = MediaHelper::resizeImage($src, $path .DS. strtolower($name), $cfgParams->get('resize_image_upload_width', 1280), $cfgParams->get('resize_image_upload_height', 1024), $cfgParams->get('resize_image_upload_quality', 80));
				JFile::delete($src);
			} else {
				if(JFile::copy($src, $path .DS. JFile::makeSafe(strtolower($name)))) {
					JFile::delete($src);
					return true;
				} else {
					JFile::delete($src);
					return false;
				}
			}
		} else {
			return false;
		}
		return true;
	}
	
	function moveFiles($src, $dest) {
		jimport('joomla.filesystem.file');
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$enable_resize = $cfgParams->get('resize_image_upload', 1);
  		
		if(is_writable($dest)) {			
			//Now we read all jpg files and put them in an array.
			$files = JFolder::files($src, '', true, true);
 			
			if($enable_resize) {
				foreach ($files as $file) {
					$fileArray = pathinfo($file);
					if(MediaHelper::isImage($file)) {
						$check = MediaHelper::resizeImage($file, $dest . DS. MediaHelper::removeWhiteSpace(JFile::makeSafe(strtolower($fileArray['basename']))), $cfgParams->get('resize_image_upload_width', 1280), $cfgParams->get('resize_image_upload_height', 1024), $cfgParams->get('resize_image_upload_quality', 80));
						JFile::delete($file);
					}
				}
			} else {
				foreach ($files as $file) {
					$fileArray = pathinfo($file);
					if(MediaHelper::isImage($file)) {
						JFile::copy($file, $dest . DS. MediaHelper::removeWhiteSpace(JFile::makeSafe(strtolower($fileArray['basename']))));
   						JFile::delete($file);
					}
				}
			}
		} else {			
			echo "Not writable";
			return false;
		}
		return true;
	}
	
	function resizeImage($src, $dest, $width, $height, $quality) {
		$img = $src;
		$path = pathinfo($img);
		$origSize = getimagesize($src);
		$reduceQuality = true;
		if($origSize[0] < $width) {
			$width = $origSize[0];
			$reduceQuality = false;
		}
		
		if($origSize[1] < $height) {
			$height = $origSize[1];
			$reduceQuality = false;
		}
		
		$xratio = $width/($origSize[0]);
		$yratio = $height/($origSize[1]);
		
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				$image = imagecreatefromjpeg($img);
				break;
			case "gif":
				$image = imagecreatefromgif($img);
				break;
			case "png":
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
			$resized = imagecreatetruecolor($width, $height);
		}
	
		imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($image),imagesy($image));
		
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				if($reduceQuality) {
					$check = imagejpeg($resized, $dest, $quality);
				} else {
					$check = imagejpeg($resized, $dest);
				}
				break;
			case "gif":
				$check = imagegif($resized, $dest);
				break;
			case "png":
				if($reduceQuality) {
					$check = imagepng($resized, $dest, $quality);
				} else {
					$check = imagepng($resized, $dest);
				}
				break;
			default:
				break;			
		}
		if($check) {
			imagedestroy($resized);
			return true;
		} else {
			return false;
		}
	}
	
	function removeWhiteSpace($name) {
		$pattern = '/\s+/';
		$replacement = '_';
		$new_name = preg_replace($pattern, $replacement, $name);
		return $new_name;
	}
}