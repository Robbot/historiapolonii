<?php defined('_JEXEC') or die('Restricted access'); ?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a href="index.php?option=com_xgallery&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;task=add&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">
						<img src="components/com_xgallery/images/folderup_32.png" width="32" height="32" border="0" alt=".." /></a>
				</div>
			</div>
			<div class="controls">
				<span>&nbsp;</span>
			</div>
			<div class="imginfoBorder">
				<a href="index.php?option=com_xgallery&amp;task=add&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" target="folderframe">..</a>
			</div>
		</div>
