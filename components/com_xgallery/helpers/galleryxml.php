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
		define('JPATH_ROOT', $_SERVER['DOCUMENT_ROOT']);
		define('DS', '/');
		require_once(JPATH_ROOT.'/components/com_xgallery/helpers'.DS.'gallery.php' );
		$cParams = GalleryHelper::getCookieParams();
		$imagPath = $cParams->bpath; //$_GET['galLocal'];
		$imageBaseFullPath = JPATH_ROOT.DS.$imagPath;
		$imageBasePath = $imagPath;
		//$imageBaseUrl = JURI::root().$imagPath;
		$galleryLocal = $_GET['collLocal']; // get the current gallery location J-Pop/Ayumi_Hamasaki
		$resWidth = $_GET['resWidth'];
		$resHeight = $_GET['resHeight'];
		$start = $_GET['s'];
		$limit = $_GET['l'];
		$maxWidth = $_GET['mw'];
		$maxHeight = $_GET['mh'];
		
		$path = $imagPath.DS.$galleryLocal.DS;
		$iterator = new DirectoryIterator($path);
		
		foreach($iterator as $file) {
			if(!$file->isDot()) {
				$tempPath = $imagPath.DS.$galleryLocal.DS.$file->getFilename();
				if(GalleryHelper::isImage($tempPath)) {
					list($origWidth, $origHeight) = getimagesize($tempPath);
					$newSize = Galleryhelper::imageResize($origWidth, $origHeight, $maxWidth);
					$dirfiles[] = array('name' => $file->getFilename(), 
										'width' => $newSize[0], 
										'height' => $newSize[1]);
				}
			}
		}

		$total = count($dirfiles);
		// Send the headers
		header('Content-type: text/xml');
		header('Pragma: public');        
		header('Cache-control: private');
		header('Expires: -1');
		echo('<?xml version="1.0" encoding="utf-8"?>');
		
		?>
		<images>
			 
			<large base ="<?php echo $imagPath.DS.$galleryLocal.DS; ?>" font = "Arial" fontsize = "3" color = "#F0F0F0" border = "0"> </large>
			<photos>	
				<?php //foreach($dirfiles as $dirfile) {
					for ($count = $start; $count < $start + $limit; $count++) {
						if($count < $total) { 
							$imageThumb = htmlspecialchars(DS.$galleryLocal.DS.$dirfiles[$count]['name']."&w=".$resWidth."&h=".$resHeight.'&tn=0');
							$imageThumb = "components/com_xgallery/helpers/img.php?file=".$imageThumb;
				?>
				<photo
				path = "<?php echo $dirfiles[$count]['name']; ?>"
				width = "<?php echo $dirfiles[$count]['width']; ?>"
				height = "<?php echo $dirfiles[$count]['height']; ?>"
				thumbpath = "<?php echo $imageThumb; ?>"
				thumbwidth = "<?php echo $resWidth; ?>"
				thumbheight = "<?php echo $resHeight; ?>">
				</photo>
				<?php } } ?>
			</photos>
		</images>
		