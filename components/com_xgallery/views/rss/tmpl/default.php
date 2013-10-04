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
	defined('_JEXEC') or die('Restricted access');

?>

<div class="componentheading">RSS</div>
<div class="blog">
<?php 
	$count = 1;
	foreach($this->collections as $collection) {
		echo "<div style='padding:5px 10px;'>{$count}. {$collection->name}</div>";	
		$count++;		
	}
?>		
</div>