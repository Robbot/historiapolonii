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
    $currRow = 0;
?>

<div id="xgallery-image-container">
	<div id="gallery" class="content">
		<div id="controls" class="controls"></div>
		<div class="slideshow-container">
			<div id="loading" class="loader"></div>
			<div id="slideshow" class="slideshow"></div>
		</div>
		<?php if($this->coll_sub_enabled) { ?><div id="caption" class="caption-container"></div><?php } ?>
	</div>
	
	<div id="thumbs" class="navigation">
		<ul class="thumbs noscript">
			<?php foreach($this->images as $image) { 
				$collLink = "file=".$image['path'].$image['name']."&amp;tn=1";
				$collString = "file=".$image['path'].$image['name']."&amp;tn=0";	
				//htmlspecialchars ($image)
			if($currRow == 0) {
			?> <li class="image-clear-left"> <?php 
			} else {	
			?> <li> <?php } ?>
				<div class="image-item-inner">
					<a class="thumb" name="leaf" href="<?php echo JURI::base(true); ?>/components/com_xgallery/helpers/img.php?<?php echo $collLink; ?>" title="<?php echo htmlspecialchars($this->collection[0]->name); ?>">
						<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/helpers/img.php?<?php echo $collString; ?>" alt="<?php echo htmlspecialchars($this->collection[0]->name); ?>" />
					</a>
				</div>
				<?php if($this->coll_show_thumb_name) { 
					$imgName = pathinfo($image['name']);
					$items = array('/\_/', '/\-/');
					$thmName = preg_replace($items, ' ', $imgName['filename']);
					
					if($this->coll_sub_enabled) {
				?>
				<div class="caption">					
					<div class="image-title"><?php echo ucwords($thmName); ?></div>
					<div class="image-desc"><?php echo htmlspecialchars($this->coll_sub); ?></div>
				</div>
				<?php } else { ?>
					<div class="image-name"><?php echo ucwords($thmName); ?></div>
				<?php } } ?>
			</li>
			<?php 
				if($currRow < ($this->num_thumbcol - 1)) {
					$currRow++;
				} else {
					//echo '<div class="image-clear"><!-- clear --></div>';
					//echo '<br class="image-clear" />';
					$currRow = 0;
				}
			} ?>
		</ul>
	</div>
</div>
<div class="image-clear"><!-- clear --></div>