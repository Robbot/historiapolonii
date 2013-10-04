<?php defined('_JEXEC') or die('Restricted access'); ?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a href="index.php?option=com_xgallery&amp;task=add&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo rawurlencode($this->_tmp_folder->path_relative); ?>" target="folderframe">
						<img src="components/com_xgallery/images/folder.png" width="80" height="80" border="0" />
					</a>
				</div>
			</div>
			<div class="controls">
				<a class="delete-item" href="index.php?option=com_xgallery&amp;task=delete&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;tmpl=component&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo rawurlencode($this->state->folder); ?>&amp;rm[]=<?php echo $this->_tmp_folder->name; ?>" rel="<?php echo $this->_tmp_folder->name; ?>' :: <?php echo $this->_tmp_folder->files+$this->_tmp_folder->folders; ?>"><img src="components/com_xgallery/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" /></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->name; ?>" />
			</div>
			<div class="imginfoBorder">
				<?php 
				if(CURR_CONT == "collections") {
					if(CURR_TYPE == 'folder') {
						$linkBack = 'index.php?option=com_xgallery&amp;task=edit&amp;controller='.CURR_CONT.'&amp;view=default&amp;id='.CURR_ID.'&amp;reqType='.CURR_TYPE.'&amp;folder='.rawurlencode($this->_tmp_folder->path_relative);			
						$linkClick = 'onClick="parent.location.href = \''.$linkBack.'\';"';
					} else {
						$linkBack = 'index.php?option=com_xgallery&amp;currCont='.CURR_CONT.'&amp;id='.CURR_ID.'&amp;reqType='.CURR_TYPE.'&amp;task=add&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder='.rawurlencode($this->_tmp_folder->path_relative);			
						$linkClick = '';
					}
				} else {
					$linkBack = 'index.php?option=com_xgallery&amp;currCont='.CURR_CONT.'&amp;id='.CURR_ID.'&amp;reqType='.CURR_TYPE.'&amp;task=add&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder='.rawurlencode($this->_tmp_folder->path_relative);			
					$linkClick = '';
				}	
				?>
				<a href="<?php echo $linkBack; ?>" <?php echo $linkClick; ?> target="folderframe"><?php echo substr( $this->_tmp_folder->name, 0, 10 ) . ( strlen( $this->_tmp_folder->name ) > 10 ? '...' : ''); ?></a>
			</div>
		</div>
