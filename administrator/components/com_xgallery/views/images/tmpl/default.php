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
defined('_JEXEC') or die('Restricted access'); ?>
<script type='text/javascript'>
var image_base_path = '<?php $params =& JComponentHelper::getParams('com_xgallery');
echo $params->get('image_path', 'images/stories');?>/';
</script>
<form action="index.php" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/dots.gif" width="22" height="12" alt="..." />
	</div>
	<fieldset>
		<div style="float: left">
			<label for="folder"><?php echo JText::_('Directory') ?></label>
			<?php echo $this->folderList; ?>
			<button type="button" id="upbutton" title="<?php echo JText::_('Directory Up') ?>"><?php echo JText::_('Up') ?></button>
		</div>
		<div style="float: right">
			<button type="button" onclick="ImageManager.onok();window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Insert') ?></button>
			<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Cancel') ?></button>
		</div>
	</fieldset>
	<iframe id="imageframe" name="imageframe" src="index.php?option=com_xgallery&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;task=add&amp;controller=folders&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>"></iframe>

	<fieldset>
		<table class="properties">
			<tr>
				<td><label for="f_url"><?php echo JText::_('Image URL') ?></label></td>
				<td><input type="text" id="f_url" value="" /></td>
				<td><label for="f_align"><?php echo JText::_('Align') ?></label></td>
				<td>
					<select size="1" id="f_align" title="Positioning of this image">
						<option value="" selected="selected"><?php echo JText::_('Not Set') ?></option>
						<option value="left"><?php echo JText::_('Left') ?></option>
						<option value="right"><?php echo JText::_('Right') ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="f_alt"><?php echo JText::_('Image description') ?></label></td>
				<td><input type="text" id="f_alt" value="" /></td>
			</tr>
			<tr>
				<td><label for="f_title"><?php echo JText::_('Title') ?></label></td>
				<td><input type="text" id="f_title" value="" /></td>
				<td><label for="f_caption"><?php echo JText::_('Caption') ?></label></td>
				<td><input type="checkbox" id="f_caption" /></td>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" id="dirPath" name="dirPath" />
	<input type="hidden" id="f_file" name="f_file" />
	<input type="hidden" id="tmpl" name="component" />
</form>
<form action="<?php echo JURI::base(); ?>index.php?option=com_xgallery&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;task=upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;pop_up=1&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo JText::_('Upload'); ?></legend>
		<fieldset class="actions">
			<input type="file" id="file-upload" name="Filedata" />
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
			<span id="upload-clear"></span>
		</fieldset>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" />
		</ul>
	</fieldset>
	<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_xgallery&amp;currCont='.CURR_CONT.'&amp;id='.CURR_ID.'&amp;reqType='.CURR_TYPE.'&amp;task=add&amp;controller=folders&view=images&tmpl=component&e_name='.JRequest::getCmd('e_name')); ?>" />
</form>
