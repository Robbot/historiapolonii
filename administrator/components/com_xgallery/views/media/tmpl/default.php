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
<?php echo $this->help_message; ?>
<table width="100%" cellspacing="0">
    <tr valign="top">
        <td width="200px">
            <fieldset id="treeview">
                <legend><?php echo JText::_( 'Folders' ); ?></legend>
                <div id="media-tree_tree"></div>
                <?php echo $this->loadTemplate('folders'); ?>
            </fieldset>
        </td>
        <td>
            <?php if ($this->require_ftp): ?>
            <form action="index.php?option=com_xgallery&amp;controller=folders&amp;task=ftpValidate" name="ftpForm" id="ftpForm" method="post">
                <fieldset title="<?php echo JText::_('DESCFTPTITLE'); ?>">
                    <legend><?php echo JText::_('DESCFTPTITLE'); ?></legend>
                    <?php echo JText::_('DESCFTP'); ?>
                    <table class="adminform nospace">
                        <tbody>
                            <tr>
                                <td width="120">
                                    <label for="username"><?php echo JText::_('Username'); ?>:</label>
                                </td>
                                <td>
                                    <input type="text" id="username" name="username" class="input_box" size="70" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td width="120">
                                    <label for="password"><?php echo JText::_('Password'); ?>:</label>
                                </td>
                                <td>
                                    <input type="password" id="password" name="password" class="input_box" size="70" value="" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <input type="hidden" name="reqType" value="<?php echo CURR_TYPE; ?>" />
                <input type="hidden" name="id" value="<?php echo CURR_ID; ?>" />
                <input type="hidden" name="currCont" value="<?php echo CURR_CONT; ?>" />
            </form>
            <?php endif; ?>

            <form action="index.php?option=com_xgallery&amp;controller=folders&amp;task=create" name="folderForm" id="folderForm" method="post">
                <fieldset id="folderview">
                    <legend><?php echo JText::_( 'Files' ); ?></legend>
                    <div class="path">
                        <input class="inputbox" type="text" id="folderpath" readonly="readonly" /><?php echo DS; ?>
                        <!-- <input class="inputbox" type="text" id="foldername" name="foldername"  /> -->
                        <input type="hidden" name="reqType" value="<?php echo CURR_TYPE; ?>" />
                        <input type="hidden" name="id" value="<?php echo CURR_ID; ?>" />
                        <input type="hidden" name="currCont" value="<?php echo CURR_CONT; ?>" />
                        <input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="<?php echo $this->state->folder; ?>" />
                        <!-- <button type="submit"><?php echo JText::_( 'Create Folder' ); ?></button> -->
                    </div>
                    <div class="view">
                        <iframe src="index.php?option=com_xgallery&amp;task=add&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;folder=<?php echo $this->state->folder;?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
                    </div>
                </fieldset>
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>

            <form action="index.php?option=com_xgallery" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="controller" value="folders"/>
                <input type="hidden" name="cb1" id="cb1" value="0" />
                <input type="hidden" name="reqType" value="<?php echo CURR_TYPE; ?>" />
                <input type="hidden" name="id" value="<?php echo CURR_ID; ?>" />
                <input type="hidden" name="currCont" value="<?php echo CURR_CONT; ?>" />
                <input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->folder; ?>" />
            </form>

            <!-- File Upload Form -->
            <div style="display:none;">
            <form action="<?php echo JURI::base(); ?>index.php?option=com_xgallery&amp;controller=file&amp;task=upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><?php echo JText::_( 'Upload File' ); ?> [ <?php echo JText::_( 'Max' ); ?>&nbsp;<?php echo ($this->config->get('upload_maxsize') / 1000000); ?>M ]</legend>
                    <fieldset class="actions">
                        <input type="file" id="file-upload" name="Filedata" />
                        <input type="submit" id="file-upload-submit" value="<?php echo JText::_('Start Upload'); ?>"/>
                        <span id="upload-clear"></span>
                    </fieldset>
                    <ul class="upload-queue" id="upload-queue">
                        <li style="display: none"></li>
                    </ul>
                </fieldset>
                <input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_xgallery'); ?>" />
                <input type="hidden" name="reqType" value="<?php echo CURR_TYPE; ?>" />
                <input type="hidden" name="id" value="<?php echo CURR_ID; ?>" />
                <input type="hidden" name="currCont" value="<?php echo CURR_CONT; ?>" />
            </form> 
            </div>
        </td>
    </tr>
</table>
