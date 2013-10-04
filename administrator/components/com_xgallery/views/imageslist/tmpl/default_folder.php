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
<div class="item">
	imageslist default folder.php
	<a href="index.php?option=com_xgallery&amp;task=add&amp;controller=folders&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>">
		<img src="<?php echo JURI::base(true); ?>/components/com_xgallery/images/folder.gif" width="80" height="80" alt="<?php echo $this->_tmp_folder->name; ?>" />
		<span><?php echo $this->_tmp_folder->name; ?></span></a>
</div>
