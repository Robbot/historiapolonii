<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: default.php 515 2011-03-22 13:45:19Z nikosdion $
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');
if(empty($this->tag)) $this->tag = null;
?>

<fieldset>
	<legend><?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?></legend>
	<?php echo $this->profilename; ?>
</fieldset>

<?php if(count($this->logs)): ?>
<form name="adminForm" action="index.php" method="post">
	<input name="option" value="com_akeeba" type="hidden" />
	<input name="view" value="log" type="hidden" />
	<fieldset>
		<label for="tag"><?php echo JText::_('LOG_CHOOSE_FILE_TITLE'); ?></label>
		<?php echo JHTML::_('select.genericlist', $this->logs, 'tag', 'onchange=submitform()', 'value', 'text', $this->tag, 'tag') ?>
		
		<?php if(!empty($this->tag)): ?>
		<input type="button" value="<?php echo JText::_('LOG_LABEL_DOWNLOAD'); ?>" onclick="window.location='<?php echo JURI::base(); ?>index.php?option=com_akeeba&view=log&task=download&tag=<?php echo urlencode($this->tag); ?>';"></input>
		<?php endif; ?>
		
		<?php if(!empty($this->tag)): ?>
		<br/>
		<hr/>
		<iframe
			src="<?php echo JURI::base(); ?>index.php?option=com_akeeba&view=log&task=iframe&layout=raw&tag=<?php echo urlencode($this->tag); ?>"
			width="99%" height="400px">
		</iframe>
		<?php endif; ?>		
				
	</fieldset>
</form>
<?php else: ?>
<fieldset>
	<h2><?php echo JText::_('LOG_NONE_FOUND') ?></h2>
</fieldset>
<?php endif; ?>