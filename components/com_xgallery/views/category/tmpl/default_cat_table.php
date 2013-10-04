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
    $collCount = count($this->collections)
?>

<div id="xgallery-cat-container">			
	<?php foreach($this->subCategories as $subCategory) { 
		$link = JRoute::_("index.php?option=com_xgallery&view=category&id={$subCategory->slug}&Itemid={$this->compid}");
	?>
	<div class="image-item" style="width:<?php echo $this->div_width; ?>%;">
		<div class="image-img">
			<div class="image-item-inner">
				<a href="<?php echo $link ;?>">
					<?php 
						$catString = "file=".'/'.urlencode($subCategory->thumb)."&amp;tn=0";
					?>
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/helpers/img.php?<?php echo $catString; ?>" alt="<?php echo htmlspecialchars($subCategory->name); ?>" />
				</a>
			</div>
		</div>
		<?php if($this->cat_show_name) {?>
		<div class="image-name">
			<?php echo htmlspecialchars($subCategory->name); ?>
		</div>
		<?php } ?>
		<?php if($this->cat_show_date) {?>
		<div class="image-date">
			<?php echo JHTML::Date($subCategory->creation_date, '%m-%d-%Y'); ?>
		</div>
		<?php } ?>
		<?php if($this->cat_show_hits) {?>
		<div class="image-hits">
			<?php echo JTEXT::_('Hits'); ?> <?php echo $subCategory->hits; ?>
		</div>
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