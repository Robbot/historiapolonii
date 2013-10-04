<?php defined('_JEXEC') or die('Restricted access'); ?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a class="img-preview" href="<?php echo JURI::root(true) . '/components/com_xgallery/helpers/img.php?file='.'/'.$this->_tmp_img->path_relative.'&amp;tn=1'; ?>" title="<?php echo $this->_tmp_img->name; ?>" style="display: block; width: 100%; height: 100%">
						<div class="image">
							<img src="<?php echo JURI::root(true) . '/components/com_xgallery/helpers/img.php?file='.'/'.$this->_tmp_img->path_relative.'&w='.$this->rWidth.'&h='.$this->rHeight.'&amp;tn=0'; ?>" width="<?php echo $this->_tmp_img->width_60; ?>" height="<?php echo $this->_tmp_img->height_60; ?>" alt="<?php echo $this->_tmp_img->name; ?> - <?php echo MediaHelper::parseSize($this->_tmp_img->size); ?>" border="0" />
						</div>
					</a>
				</div>
			</div>
			<div class="controls">
				<a class="delete-item" href="index.php?option=com_xgallery&amp;task=delete&amp;currCont=<?php echo CURR_CONT; ?>&amp;controller=file&amp;view=mediaList&amp;id=<?php echo CURR_ID; ?>&amp;tmpl=component&amp;<?php echo JUtility::getToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>&amp;rm[]=<?php echo $this->_tmp_img->name; ?>" rel="<?php echo $this->_tmp_img->name; ?>"><img src="components/com_xgallery/images/remove.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Delete' ); ?>" /></a>
				<?php //<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; " /> ?>
			</div>
			<div class="imginfoBorder">
				<?php 				
				if(CURR_CONT == "categories") {
					if(!isset($this->_tmp_folder->path_relative)) {
							$folderPath = JRequest::getVar('folder');
						} else {
							$folderPath = $this->_tmp_folder->path_relative;
						}
					$linkBack = 'index.php?option=com_xgallery&amp;imgPath='.$this->_tmp_img->path_relative.'&amp;task=edit&amp;controller='.CURR_CONT.'&amp;view=mediaList&amp;id='.CURR_ID.'&amp;folder='.$folderPath;			
					$linkClick = 'onClick="parent.location.href = \''.$linkBack.'\';"';
				} else {					
					if(CURR_TYPE == 'folder') {
						$linkBack = COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative;			
						$linkClick = '';
					} else {
						if(!isset($this->_tmp_folder->path_relative)) {
							$folderPath = JRequest::getVar('folder');
						} else {
							$folderPath = $this->_tmp_folder->path_relative;
						}
						$linkBack = 'index.php?option=com_xgallery&amp;imgPath='.$this->_tmp_img->path_relative.'&amp;task=edit&amp;controller='.CURR_CONT.'&amp;view=mediaList&amp;id='.CURR_ID.'&amp;folder='.$folderPath;			
						$linkClick = 'onClick="parent.location.href = \''.$linkBack.'\';"';
					}					
				}	
				?>
				<a href="<?php echo $linkBack; ?>" <?php echo $linkClick; ?> class="preview"><?php echo $this->escape( substr( $this->_tmp_img->name, 0, 10 ) . ( strlen( $this->_tmp_img->name ) > 10 ? '...' : '')); ?></a>
			</div>
			
		</div>
