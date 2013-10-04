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
    $currRow2 = 0;
?>

<div id="xgallery-image-container">
<?php 
	foreach($this->collections as $collection) {
		$link = JRoute::_("index.php?option=com_xgallery&view=single&catid={$collection->catslug}&id={$collection->slug}&Itemid={$this->compid}");
?>
	<div class="image-item" style="width:<?php echo $this->div_width; ?>%;">
		<div class="image-img">
			<div class="image-item-inner">
				<a href="<?php echo $link ;?>">
				<?php 
					$collString = "file=".'/'.urlencode($collection->thumb)."&amp;tn=0";
				?>
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/helpers/img.php?<?php echo $collString; ?>" alt="<?php echo htmlspecialchars($collection->name); ?>" />
				</a>
			</div>
		</div>
		<?php if($this->show_name) {?>
		<div class="image-name">
			<?php echo htmlspecialchars($collection->name); ?>
		</div>
		<?php } ?>
		<?php if($this->show_date) {?>
		<div class="image-date">
			<?php echo JHTML::Date($collection->creation_date, '%m-%d-%Y'); ?>
		</div>
		<?php } ?>
		<?php if($this->show_hits) {?>
		<div class="image-date">
			<?php echo JTEXT::_('Hits'); ?> <?php echo $collection->hits; ?>
		</div>
		<?php } ?>
	</div>
	<?php
		if($currRow2 < ($this->num_thumbcol - 1)) {
			$currRow2++;
		} else {
			echo '<div class="image-clear"><!-- clear --></div>';
			$currRow2 = 0;
		}
	}
	?>
	<div class="image-clear"><!-- clear --></div>
</div>
	