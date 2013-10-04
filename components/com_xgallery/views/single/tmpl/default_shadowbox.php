<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component xgallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
    defined('_JEXEC') or die('Restricted access');
    $currRow = 0;
?>

<div id="xgallery-image-container">
	<?php

		foreach($this->images as $image) {
		?>
			<div class="image-item" style="width:<?php echo $this->div_width; ?>%;">
				<?php 
				if($this->filter_images || $this->enable_watermark || $this->image_external) {
					$collLink =  JURI::base(true)."/components/com_xgallery/helpers/img.php?file=".$image['path'].$image['name']."&amp;tn=1";
				} else {
					$collLink = JURI::base(true)."/".$this->image_path.$image['path'].$image['name'];
				}
				
				$collString = "file=".$image['path'].$image['name']."&amp;tn=0";	
				?>
				<div class="image-item-inner">
					<a href="<?php echo $collLink; ?>" rel="<?php echo $this->shadowbox; ?>" title="<?php echo htmlspecialchars($this->collection[0]->name); ?>" <?php if($this->coll_sub_enabled) { echo 'rev="' . htmlspecialchars($this->coll_sub) . '"'; } ?>>					
						<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/helpers/img.php?<?php echo $collString; ?>" alt="<?php echo htmlspecialchars($this->collection[0]->name); ?>" />
					</a>
				</div>
				<?php if($this->coll_show_thumb_name) { 
					$imgName = pathinfo($image['name']);
					$items = array('/\_/', '/\-/');
					$thmName = preg_replace($items, ' ', $imgName['filename']);
				?>
				<div class="image-name"><?php echo ucwords($thmName); ?></div>
				<?php } ?>				
			</div>
		<?php 
		
			if($currRow < ($this->num_thumbcol - 1)) {
				$currRow++;
			} else {
				echo '<div class="image-clear"><!-- clear --></div>';
				$currRow = 0;
			}
		}
	?>
		<div class="image-clear"><!-- clear --></div>
	</div>	
<div class="image-clear"><!-- clear --></div>