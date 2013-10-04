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
	<div id="cpanel" class="xgallery-info-section-left">
		<div class="icon xgallery-info-buttons">
			<a href="<?php echo JURI::base(true); ?>/index.php?option=com_xgallery&controller=categories&view=single">
				<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/Photo-Folder-smooth-icon.png" />
				<span><?php echo JText::_('ADD CATEGORY'); ?></span>
			</a>			
		</div>
		<div class="icon xgallery-info-buttons">
			<a href="<?php echo JURI::base(true); ?>/index.php?option=com_xgallery&controller=categories&view=collection">
				<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/Pictures-Folder-smooth-icon.png" />
				<span><?php echo JText::_('ADD COLLECTION'); ?></span>
			</a>
		</div>
		<div class="xgallery-clear"><!-- clear --></div>
		<div class="xgallery-info-modules">
			<div class="xgallery-info-heading"><?php echo JText::_('INSTALLED MODULES'); ?></div>
			<?php if($this->xgfce['ok']) { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-fce-icon.png" /><br />
					<span class="xgallery-ok"><?php echo $this->xgfce['mesg']; ?></span>
				</div>
			<?php } else { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-fce-icon-no.png" /><br />
					<span class="xgallery-error"><?php echo $this->xgfce['mesg']; ?></span>
				</div>
			<?php } ?>
			
			<?php if($this->xgscroller['ok']) { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-jscroller-icon.png" /><br />
					<span class="xgallery-ok"><?php echo $this->xgscroller['mesg']; ?></span>
				</div>
			<?php } else { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-jscroller-icon-no.png" /><br />
					<span class="xgallery-error"><?php echo $this->xgscroller['mesg']; ?></span>
				</div>
			<?php } ?>
			
			<?php if($this->xgmenu['ok']) { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-menu-icon.png" /><br />
					<span class="xgallery-ok"><?php echo $this->xgmenu['mesg']; ?></span>
				</div>
			<?php } else { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-menu-icon-no.png" /><br />
					<span class="xgallery-error"><?php echo $this->xgmenu['mesg']; ?></span>
				</div>
			<?php } ?>
			
			<?php if($this->xgcollection['ok']) { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-collection-icon.png" /><br />
					<span class="xgallery-ok"><?php echo $this->xgcollection['mesg']; ?></span>
				</div>
			<?php } else { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-collection-icon-no.png" /><br />
					<span class="xgallery-error"><?php echo $this->xgcollection['mesg']; ?></span>
				</div>
			<?php } ?>						 
		</div>
		<div class="xgallery-clear"><!-- clear --></div>
		<div class="xgallery-info-modules">
			<div class="xgallery-info-heading"><?php echo JText::_('INSTALLED PLUGINS'); ?></div>
			<?php if($this->xgsearch['ok']) { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-search-icon.png" /><br />
					<span class="xgallery-ok"><?php echo $this->xgsearch['mesg']; ?></span>
				</div>
			<?php } else { ?>
				<div class="icon xgallery-info-modules-item">
					<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/xgallery-search-icon-no.png" /><br />
					<span class="xgallery-error"><?php echo $this->xgsearch['mesg']; ?></span>
				</div>
			<?php } ?>			 
		</div>		
	</div>
	<div class="xgallery-info-section-right">
		<div class="xgallery-info-section-info">
			<div class="xgallery-info-section-author"><span><?php echo JText::_('AUTHOR'); ?>:</span> <?php echo $this->info['author']; ?></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('VERSION'); ?>:</span> <?php echo $this->info['version']; ?></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('COPYRIGHT'); ?>:</span> <?php echo $this->info['copyright']; ?></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('AUTHOR URL'); ?>:</span> <a href="<?php echo $this->info['authorurl']; ?>"><?php echo $this->info['authorurl']; ?></a></div>
			<div class="xgallery-info-section-author"><span><?php echo JText::_('GPL'); ?>:</span> <a href="<?php echo $this->info['gpllink']; ?>"><?php echo $this->info['gpl']; ?></a></div>
		</div>
		<div class="xgallery-info-section-logo">
			<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/pictures-icon-logo.png" />
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
