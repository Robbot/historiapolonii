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
            
            <form action="index.php?option=com_xgallery&amp;task=add&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;task=create" name="folderForm" id="folderForm" method="post">
                <fieldset id="folderview">
                    <legend><?php echo JText::_( 'Files' ); ?></legend>
                     
                    <div class="view">
                        <iframe src="index.php?option=com_xgallery&amp;task=add&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;id=<?php echo CURR_ID; ?>&amp;folder=<?php echo urlencode($this->state->folder);?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0"></iframe>
                    </div>
                </fieldset>
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>

            <form action="index.php?option=com_xgallery" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="cb1" id="cb1" value="0" />
                <input type="hidden" name="controller" value="folders" />
                <input type="hidden" name="currCont" value="<?php echo CURR_CONT; ?>" />
                <input type="hidden" name="id" value="<?php echo CURR_ID; ?>" />
                <input type="hidden" name="reqType" value="<?php echo CURR_TYPE; ?>" />
                <input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->folder; ?>" />
                <?php echo JHTML::_('form.token'); ?>
            </form>
        </td>
    </tr>
</table>
