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

<ul <?php echo $this->folders_id; ?>>
<?php foreach ($this->folders['children'] as $folder) : ?>
	<li id="<?php echo $folder['data']->relative; ?>">
		<a href="index.php?option=com_xgallery&amp;task=add&amp;currCont=<?php echo CURR_CONT; ?>&amp;id=<?php echo CURR_ID; ?>&amp;reqType=<?php echo CURR_TYPE; ?>&amp;controller=folders&amp;view=mediaList&amp;tmpl=component&amp;folder=<?php echo $folder['data']->relative; ?>" target="folderframe"><?php echo $folder['data']->name; ?></a><?php echo $this->getFolderLevel($folder); ?></li>
<?php endforeach; ?>
</ul>
