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
    
    JToolBarHelper::title( JText::_( 'XGallery Main' ), 'generic.png' );
	JToolBarHelper::preferences('com_xgallery', 425);
	
	$count = 1;
?>

<div id="xgallery-info-container">
	<div class="xgallery-info-section-left">
		<div class="xgallery-info-buttons">
			<a href="/administrator/index.php?option=com_xgallery&controller=categories&view=single">
				<img src="/administrator/components/com_xgallery/images/Photo-Folder-smooth-icon.png" />
			</a>
		</div>
		<div class="xgallery-info-buttons">
			<a href="/administrator/index.php?option=com_xgallery&controller=categories&view=collection">
				<img src="/administrator/components/com_xgallery/images/Pictures-Folder-smooth-icon.png" />
			</a>
		</div>
		<div class="xgallery-clear"><!-- clear --></div>
		<div class="xgallery-info-modules">
			<?php if($this->search['ok']) { ?>
				<?php echo JText::_('SEARCH'); ?>: <span class="xgallery-ok"><?php echo $this->search['mesg']; ?></span><br/>
			<?php } else { ?>
				<?php echo JText::_('SEARCH'); ?>: <span class="xgallery-error"><?php echo $this->search['mesg']; ?></span><br/>
			<?php } ?>
	
			<?php if($this->shadowbox['ok']) { ?>
				<?php echo JText::_('SHADOWBOX'); ?>: <span class="xgallery-ok"><?php echo $this->shadowbox['mesg']; ?></span><br/>
			<?php } else { ?>
				<?php echo JText::_('SHADOWBOX'); ?>: <span class="xgallery-error"><?php echo $this->shadowbox['mesg']; ?></span><br/>
			<?php } ?>
			<!-- 
			<?php if($this->gallerylocation['ok']) { ?>
				<?php echo JText::_('GALLERY LOCATION'); ?>: <span class="xgallery-ok"><?php echo $this->gallerylocation['mesg']; ?></span><br/>
			<?php } else { ?>
				<?php echo JText::_('GALLERY LOCATION'); ?>: <span class="xgallery-error"><?php echo $this->gallerylocation['mesg']; ?></span><br/>
			<?php } ?>
			 -->
		</div>		
	</div>
	<div class="xgallery-info-section-right">
		<div class="xgallery-info-section-info">
			<div class="xgallery-info-section-author"><span><?php echo JText::_('AUTHOR'); ?></span> <?php echo $this->info->author; ?></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('COPYRIGHT'); ?></span> <?php echo $this->info->copyright; ?></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('AUTHOR URL'); ?></span> <a href="<?php echo $this->info->authorurl; ?>"><?php echo $this->info->authorurl; ?></a></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('GPL'); ?></span> <a href="<?php echo $this->info->gpllink; ?>"><?php echo $this->info->gpl; ?></a></div>
		</div>
		<div class="xgallery-info-section-logo">
			<img src="" />
		</div>
		<div class="xgallery-clear"><!-- clear --></div>	
	</div>
	<div class="xgallery-clear"><!-- clear --></div>
</div>
<div id="xgallery-stats-container">
	<div class="xgallery-subcontainer">
		<div class="xgallery-main-heading"><?php echo JText::_('NEW COLLECTIONS'); ?></div>
		<?php if(count($this->newcollections) > 0) { ?>		
			<div class="xgallery-main-content">
				<div>
					<div class="xgallery-left xgallery-content-hnum"><?php echo JText::_('SEARCH'); ?></div>
					<div class="xgallery-left xgallery-content-hname"><?php echo JText::_('Name'); ?></div>
					<div class="xgallery-left xgallery-content-hhits"><?php echo JText::_('Date'); ?></div>
					<div class="xgallery-clear"></div>
				</div>
			<?php foreach($this->newcollections as $newcollection) { ?>
				<div>
					<div class="xgallery-left xgallery-content-num"><?php echo $count; ?></div>
					<div class="xgallery-left xgallery-content-name"><?php echo $newcollection->name; ?></div>
					<div class="xgallery-left xgallery-content-hits"><?php echo $newcollection->creation_date; ?></div>
					<div class="xgallery-clear"></div>
				</div>
			<?php $count++; } ?>
			</div>
		<?php } else { ?>
			<?php echo JText::_('NO CATEGORIES FOUND'); ?>
		<?php } ?>
	</div>
	<div class="xgallery-subcontainer">
		<div class="xgallery-main-heading"><?php echo JText::_('TOP COLLECTIONS'); ?></div>
		<?php if(count($this->collections) > 0) { $count = 1;?>		
			<div class="xgallery-main-content">
				<div>
					<div class="xgallery-left xgallery-content-hnum"><?php echo JText::_('SEARCH'); ?></div>
					<div class="xgallery-left xgallery-content-hname"><?php echo JText::_('Name'); ?></div>
					<div class="xgallery-left xgallery-content-hhits"><?php echo JText::_('Hits'); ?></div>
					<div class="xgallery-clear"></div>
				</div>
			<?php foreach($this->collections as $collection) { ?>
				<div>
					<div class="xgallery-left xgallery-content-num"><?php echo $count; ?></div>
					<div class="xgallery-left xgallery-content-name"><?php echo $collection->name; ?></div>
					<div class="xgallery-left xgallery-content-hits"><?php echo $collection->hits; ?></div>
					<div class="xgallery-clear"></div>
				</div>
			<?php $count++; } ?>
			</div>
		<?php } else { ?>
			<?php echo JText::_('NO COLLECTIONS FOUND'); ?>
		<?php } ?>
	</div>
	<div class="xgallery-clear"><!-- clear --></div>
</div>
