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
defined( '_JEXEC' ) or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'XGallery Help' ), 'generic.png' );

?>
<div id="xgallery-help-container">
	<div id="descmain">
			This section will provide you with some hints to get around the Joomla 
			Gallery. Visit <a href="http://www.optikool.com" target="_blank">optikool.com</a> for any problems or updates. Released under
			the <a target="_blank" href="http://www.gnu.org/licenses/gpl-2.0.html">GNU/GPL License</a>.
	</div>
	<div id="desc">				
		<div>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="10982966">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
		<div class="newsfeed">
			<div class="newstitle">
				<a href="<?php echo str_replace( '&', '&amp', $this->feed->link ); ?>" target="_blank">
					<?php echo $this->feed->title; ?>
				</a>
			</div>
			
			<?php
			
			for ($j = 0; $j < $this->totalItems; $j ++) {
				$currItem = & $this->feed->items[$j];
				// item title
				?>
				<div class="newsitem">
				<?php
				if ( !is_null( $currItem->get_link() ) ) {
				?>
					<a href="<?php echo $currItem->get_link(); ?>" target="_blank">
					<?php echo $currItem->get_title(); ?></a>
				<?php
				}

				// item description
				
				// item description
				$text = $currItem->get_description();
				$text = str_replace('&apos;', "'", $text);

				// word limit check
					/*if ($words) {
						$texts = explode(' ', $text);
						$count = count($texts);
						if ($count > $words) {
							$text = '';
							for ($i = 0; $i < $words; $i ++) {
								$text .= ' '.$texts[$i];
							}
							$text .= '...';
						}
					}*/
				?>
				<div class="newsfeed_item"  >
					<?php echo $text; ?>
				</div>

				</div>
				<?php
			}
			?>
		</div>
	</div>
	<div id="content">
		<div class="heading">Joomla Gallery:</div>
		<div class="content">
			This is the main page. This page list latest ten collections added and most viewed collections. Here you can also find out 
			if the Search Plug-in and Shadowbox Plug-in is installed and enabled. The Search plug-in is needed if you want your gallery 
			to show up in searches. The Shadowbox Plug-in is need to display images in a shadowbox lightbox effect. Shadowbox requires 
			jquery installed on your website.
		</div>
		<div class="heading">Manage Categories:</div>
		<div class="content">
			This is where you can create your categories. When you first install XGallery, a Main category is created. This is your root 
			and will be used when you create a new Menu Item as type Main.  This should not be deleted. 
		</div>
		<div class="content">
			When you create a new category you will be able to choose a thumbnail after you've filled out the details and hit apply.  When 
			you browse for a thumbnail, you need to click the thumbnail name to select it.
		</div>
		<div class="heading">Manage Collections:</div>
		<div class="content">
			Collections are a group of images.  The collection tab works the same as the Category tab except when you create a new collection 
			and hit apply, you need to browse to the folder where the collection is located. You need to click the name of the folder to 
			select it. After hitting the apply button again, you will then be able to select a thumbnail. You can also create folders and 
			upload images to these folders.
		</div>
		<div class="heading">File Browser:</div>
		<div class="content">
			The file browser works similar to the Media Manager, with the exception that you need to click the folder or image name to 
			select it. Depending on what you are browsing for will determine what can be selected. 
		</div>
		<div class="heading">Menu Items:</div>
		<div class="content">
			<span>Category</span> - Shows collections in a category. You need to select a category from the categories you created in the Manage Categories 
			page.  Collections assigned to this category will be displayed.
		</div>
		<div class="content">
			<span>Main</span> - This is the main landing page. This page will use the Main category from the Manage Categories page.
		</div>
		<div class="content">
			<span>Single Collection</span> - Shows a single collection of images. You need to select a collection for this menu item.  This does not need to be 
			a assigned to a category menu item. It can be a standalone item in your menu.
		</div>
		<div class="heading">Notes about the gallery:</div>
		<div class="content">
			Be sure to go to the <span>Parameters Configuration Menu</span> and save your settings before using the gallery. 
		</div>
		<div class="content">
			You do not need to create thumbnail, but you should make your image file size web compatible. This emails if you downloaded a bunch of 
			images from you digital camera, most images will be 3 or 4 megs in size. You should use a batch imaging program like FastStone Image 
			viewer to do a batch process on your images and make those 3 to 4 mb images 50k to 150k images. This will speed up the display of 
			images on your website. 
		</div>
		<div class="content">
			The best and safest way to upload your images is to use an FTP program link WinSCP. Leaving your image directory writable can present 
			a security risk. And as a best practice you should not have any space in your image names or folder names as some browser may have 
			difficulty interpreting this.
		</div>
		<div class="heading">Additional Help</div>
		<div class="content">
			<span>Documentation</span> - <a href="http://www.optikool.com/documentation/xgallery-component" target="_blank">XGallery Documentation</a>
		</div>
		<div class="content">
			<span>Demos</span> - <a href="http://www.optikool.com/demos" target="_blank">XGallery Demos</a>
		</div>
	</div>
	<div class="xgallery-clear"><!-- clear --></div>
</div>