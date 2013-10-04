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
	JHTML::_('behavior.calendar');
	JHTML::_('behavior.tooltip');
	
	$editor =& JFactory::getEditor();
	
	if($this->row->id) {
		JToolBarHelper::title(JText::_('EDIT CATEGORY'), 'addedit.png');
	} else {
		JToolBarHelper::title(JText::_('ADD CATEGORY'), 'addedit.png');
	}
	
	JToolBarHelper::save();		
	JToolBarHelper::apply();
	
	if($this->row->id) {
		JToolBarHelper::cancel('cancel', JText::_('CLOSE'));
	} else {
		JToolBarHelper::cancel();
	}
	
	$help = "<img src=components/com_xgallery/images/help.png>";
	
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		if(pressbutton == 'save' || pressbutton == 'apply') {
			var quicktake = <?php echo $editor->getContent('quicktake'); ?>

			if (document.adminForm.name.value == '') {
				alert("<?php echo JText::_('NO NAME CATEGORY'); ?>");
			} else {
				submitform(pressbutton);
			}
		} else {
			submitform(pressbutton);			
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_('DETAILS'); ?></legend>
		<div style="float:left; width:74%;">
		<table class="admintable">
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE NAME'), JText::_('NAME'), 'tooltip.png', $help, '', false);?></div><?php echo JText::_('NAME');	?>:</td>
				<td>
					<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->row->name;?>" />
				</td>
			</tr>
			<?php if(!empty($this->pid) || $this->pid != '') { ?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE PARENT'), JText::_('PARENT'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('PARENT') ?>:</td>
				<td>
					<?php echo $this->pid;?>
				</td>
			</tr>
			<?php } ?>
			
			<?php if(!empty($this->thumblocal) || $this->thumblocal != '') {?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE BROWSE THUMBNAIL'), JText::_('BROWSE THUMBNAIL'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('BROWSE THUMBNAIL') ?>:</td> 
				<td>
					<?php echo $this->thumblocal; ?>
				</td>
			</tr>
			<?php } ?>
			<?php if(!empty($this->thumb) || $this->thumb != '') {?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE THUMBNAIL'), JText::_('THUMBNAIL'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('THUMBNAIL') ?>:</td> 
				<td>
					<?php echo $this->thumb; ?>
				</td>
			</tr>
			<?php } ?>		
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE QUICKTAKE'), JText::_('QUICKTAKE'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('QUICKTAKE') ?>:</td>
				<td>
					<?php echo $this->editor->display('quicktake', $this->row->quicktake, '100%', '150', '40', '5');?>
				</td>
			</tr>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE DESCRIPTION'), JText::_('DESCRIPTION'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('DESCRIPTION') ?>:</td>
				<td>
					<?php
        				echo $this->editor->display( 'description',  $this->row->description, '100%', '250', '40', '10' ) ;
        			?>
				</td>
			</tr>			
		</table>
		</div>
		<div style="float:right; width:25%;">
			<?php 
			$title = JText::_( 'GENERAL OPTIONS' );
			echo $this->pane->startPane("content-pane");
			echo $this->pane->startPanel( $title, "detail-page" );
			?>
			<table class="paramlist admintable" width="100%" cellspacing="1">
				<tr>
					<td class="paramlist_key" width="40%"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE HITS'), JText::_('HITS'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('HITS') ?>:</td>
					<td class="paramlist_value"><input type="text" value="<?php echo $this->row->hits;?>" id="hits" name="hits" /></td>
				</tr>
				<tr>
					<td class="paramlist_key" width="40%"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE CREATION DATE'), JText::_('CREATION DATE'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('CREATION DATE') ?>:</td>
					<td class="paramlist_value"><?php echo $this->creation_date;?></td>
				</tr>
				<tr>
					<td class="paramlist_key" width="40%"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE PUBLISHED'), JText::_('PUBLISHED'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('PUBLISHED') ?>:</td>
					<td class="paramlist_value"><?php echo $this->published;?></td>
				</tr>
				<tr>
					<td class="paramlist_key" width="40%"><div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN ACCESS'), JText::_('ACCESS'), 'tooltip.png', $help , '', false);?></div><?php echo JText::_('ACCESS') ?>:</td>
					<td class="paramlist_value"><?php echo JHTML::_('list.accesslevel', $this->row);?></td>
				</tr>
			</table>
			<?php 
			echo $this->pane->endPanel();
				
			echo $this->pane->startPane("content-pane");
			$title = JText::_( 'METADATA INFORMATION' );
			echo $this->pane->startPanel( $title, "metadata-page" );
			?>
			<table width="100%" cellspacing="1" class="paramlist admintable">
				<tr>
					<td width="40%" class="paramlist_key">
						<div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE META DESCRIPTION'), JText::_('META DESCRIPTION'), 'tooltip.png', $help , '', false);?></div><span class="editlinktip"><label for="metadescription" id="metadescription-lbl"><?php echo JText::_( 'META DESCRIPTION' ); ?></label></span>
					</td>
					<td class="paramlist_value">
						<textarea id="metadescription" class="text_area" rows="5" cols="30" name="metadesc"><?php echo $this->row->metadesc;?></textarea>
					</td>
				</tr>
				<tr>
					<td width="40%" class="paramlist_key">
						<div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE META KEYWORDS'), JText::_('META KEYWORDS'), 'tooltip.png', $help , '', false);?></div><span class="editlinktip"><label for="metakeywords" id="metakeywords-lbl"><?php echo JText::_( 'META KEYWORDS' ); ?></label></span>
					</td>
					<td class="paramlist_value">
						<textarea id="metakeywords" class="text_area" rows="5" cols="30" name="metakey"><?php echo $this->row->metakey;?></textarea>
					</td>
				</tr>
				<tr>
					<td width="40%" class="paramlist_key">
						<div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE META AUTHORS'), JText::_('META AUTHORS'), 'tooltip.png', $help , '', false);?></div><span class="editlinktip"><label for="metakeywords" id="metakeywords-lbl"><?php echo JText::_( 'META AUTHORS' ); ?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" value="<?php echo $this->row->metaauthor;?>" id="metaauthor" name="metaauthor">						
					</td>
				</tr>
				<tr>
					<td width="40%" class="paramlist_key">
						<div style="width:20px; text-align:left; float:left;"><? echo JHTML::tooltip(JText::_('ADMIN SINGLE META ROBOTS'), JText::_('META ROBOTS'), 'tooltip.png', $help , '', false);?></div><span class="editlinktip"><label for="metarobots" id="metarobots-lbl"><?php echo JText::_( 'META ROBOTS' ); ?></label></span>
					</td>
					<td class="paramlist_value">
						<input type="text" value="<?php echo $this->row->metarobots;?>" id="metarobots" name="metarobots">						
					</td>
				</tr>		
			</table>
			<?php 
			echo $this->pane->endPanel();
			echo $this->pane->endPane();
			?>
		</div>
	</fieldset>
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="reqType" value="" id="reqType" />
	<input type="hidden" name="currCont" value="categories" />
	<input type="hidden" name="task" value="" id="task" />
	<input type="hidden" name="controller" value="categories" id="controller" />
	<input type="hidden" name="ordering" value="<?php echo $this->row->ordering;?>" />
	<input type="hidden" name="rq" value="<?php echo $this->rq;?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>			
<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
