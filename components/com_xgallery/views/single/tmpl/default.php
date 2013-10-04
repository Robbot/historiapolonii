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
    $backid = $this->router->getVar('catid');    
    $backlink = JRoute::_("index.php?option=com_xgallery&view=category&id={$backid}&Itemid={$this->compid->id}");
?>

<?php if($this->coll_show_coll_title) { ?>
<div class="componentheading<?php echo $this->moduleclass_sfx; ?>"><?php echo htmlspecialchars($this->collection[0]->name); ?></div>
<?php } ?>
<div class="blog<?php echo $this->moduleclass_sfx; ?>">
	<?php if($this->collectiondesc != '') { ?>
	<div id="xgallery-desc">
		<?php if($this->coll_show_name || $this->coll_show_date || $this->coll_show_hits) { ?>
			<div id="xgallery-info">
				<?php if($this->coll_show_name) {?>
				<div class="image-name">
					<?php echo htmlspecialchars($this->collection[0]->name); ?>
				</div>
				<?php } ?>
				<?php if($this->coll_show_date) {?>
				<div class="image-date">
					<?php echo JTEXT::_('DATE ADDED'); ?> <?php echo JHTML::Date($this->collection[0]->creation_date, '%m-%d-%Y'); ?>
				</div>
				<?php } ?>
				<?php if($this->coll_show_hits) {?>
				<div class="image-hits">
					<?php echo JTEXT::_('Hits'); ?> <?php echo $this->collection[0]->hits; ?>
				</div>
				<?php } ?>
			</div>
		<?php } ?>	
		<?php echo $this->collectiondesc; ?>		
	</div>
	<?php } ?>
	<div class="image-clear"><!-- clear --></div>	
	<?php 
		echo $this->loadTemplate($this->display_layout);
		 
		if($this->enable_coll_page) { ?>
			<div id="pagin-footer" >
				<?php if($this->isgal) { ?>
					<div class="pagin-item"><?php echo $this->pagination->getPagesLinks(); ?></div>
				<?php } else { ?>
					<div class="pagin-item"><?php echo $this->pagelinks; ?></div>
				<?php } ?>
				<div class="pagin-item"><?php echo $this->pagination->getPagesCounter(); ?></div>
			</div>
		<?php }		
	?>	
</div>
