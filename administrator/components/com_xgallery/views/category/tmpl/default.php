<?php
    defined('_JEXEC') or die('Restricted access');
	JHTML::_('behavior.calendar');
	$editor =& JFactory::getEditor();
	
	if($this->row->id) {
		JToolBarHelper::title(JText::_('EDIT CATEGORY'), 'addedit.png');
	} else {
		JToolBarHelper::title(JText::_('ADD CATEGORY'), 'addedit.png');
	}
	
	JToolBarHelper::save();	
	JToolBarHelper::apply();
	
	if($this->row->id) {
		JToolBarHelper::cancel('cancel', 'Close');
	} else {
		JToolBarHelper::cancel();
	}
	
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		if(pressbutton == 'save' || pressbutton == 'apply') {
			var quicktake = <?php echo $editor->getContent('quicktake'); ?>

			if (document.adminForm.name.value == '') {
				alert('<?php echo JTEXT::_('NAME FIELD BLANK'); ?>');
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
		<legend>Details</legend>
		<table class="admintable">
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('Name') ?>:</td>
				<td>
					<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->row->name;?>" />
				</td>
			</tr>
			<?php if(!empty($this->pid) || $this->pid != '') { ?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('Parent') ?>:</td>
				<td>
					<?php echo $this->pid;?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('UPLOAD THUMBNAIL') ?>:</td> 
				<td>
					<input type="file" name="file_upload" />
					<span><?php echo $this->thumb_writable; ?></span>			
				</td>
			</tr>
			<?php if(!empty($this->thumb) || $this->thumb != '') {?>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('THUMBNAIL') ?>:</td> 
				<td>
					<?php echo $this->thumb; ?>
				</td>
			</tr>
			<?php } ?>		
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('Quicktake') ?>:</td>
				<td>
					<?php echo $this->editor->display('quicktake', $this->row->quicktake, '100%', '150', '40', '5');?>
				</td>
			</tr>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('DESCRIPTION') ?>:</td>
				<td>
					<?php
        				echo $this->editor->display( 'description',  $this->row->description, '100%', '250', '40', '10' ) ;
        			?>
				</td>
			</tr>			
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('CREATION DATE') ?>:</td>
				<td>
					<?php echo $this->creation_date;?>
				</td>
			</tr>
			<tr>
				<td style="width:100px; text-align:right;" class="key"><?php echo JText::_('Published') ?>:</td>
				<td>
					<?php echo $this->published;?>
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="ordering" value="<?php echo $this->row->ordering;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="categories" />
	<?php echo JHTML::_('form.token'); ?>
</form>
			
