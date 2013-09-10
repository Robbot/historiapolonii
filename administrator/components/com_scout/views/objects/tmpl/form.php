<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" enctype="multipart/form-data" >

	<fieldset>
		<legend><?php echo JText::_('Form'); ?></legend>
			<table class="admintable">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_( 'Name' ); ?>:
					</td>
					<td>
						<input name="object_name" value="<?php echo @$row->object_name; ?>" size="48" maxlength="250" type="text" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'Value' ); ?>:
                    </td>
                    <td>
                        <input name="object_value" value="<?php echo @$row->object_value; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo @$row->object_id; ?>" />
			<input type="hidden" name="task" value="" />
	</fieldset>
</form>