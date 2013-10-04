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
    $collCount = count($this->collections);
?>

<?php if($this->show_main_title) {?>
<div class="componentheading"><?php echo htmlspecialchars($this->heading); ?></div>
<?php } ?>
<div class="blog">
	<?php if($this->enable_rss && $this->show_rss_feed) {?>
	<div id="rss">
		<a href="<?php echo JURI::root(); ?>index.php?option=com_xgallery&controller=feed&view=rss&id=&format=feed" rel="alternate" type="application/rss+xml">
			<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/feedIcon.png" alt="RSS Feed" style="border:none; width:15px;" class="rss-feed" />
		</a>
	</div>
	<?php } ?>
	<?php if($this->maindesc != '') { ?>
	<div id="xgallery-desc"><?php echo $this->maindesc; ?></div>
	<?php } ?>
	<?php if(count($this->subCategories) > 0 && $this->show_subcat) { ?>
		<?php if($this->show_cat_title) {?>
		<div class="componentheadingsub"><?php echo JTEXT::_('CATEGORIES'); ?></div>
		<?php } ?>
		<?php echo $this->loadTemplate($this->cat_display_layout); ?>			
	<?php } ?>
	
	<?php if($this->show_coll_title && $collCount > 0) {?>
	<div class="componentheadingsub"><?php echo JTEXT::_('COLLECTIONS'); ?></div>	
	<?php } ?>
	
	<?php if($this->show_colls) { 
		if($collCount > 0) { ?>	
			<?php echo $this->loadTemplate($this->display_layout); ?>
			<div id="pagin-footer" >
				<div class="pagin-item"><?php echo $this->pagination->getPagesLinks(); ?></div>
				<div class="pagin-item"><?php echo $this->pagination->getPagesCounter(); ?></div>
			</div>
		<?php } else { ?>
			<div id="xgallery-image-container">
				<!-- <?php echo JTEXT::_('COLLECTION NOT FOUND'); ?> -->
			</div>
		<?php } ?>
	<?php } ?>	
</div>