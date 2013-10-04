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

JToolBarHelper::title( JText::_( 'XGALLERY CATEGORIES' ), 'generic.png' );
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::preferences('com_xgallery', 425);
JToolBarHelper::editList();
JToolBarHelper::deleteList(JText::_('CONFIRM DELETE CATEGORY'));
JToolBarHelper::addNew();
?>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm">
<table>
	<tr>
		<td style="text-align:left; width:100%;">
		<?php echo JText::_('SEARCH'); ?>: 
		<input type="text" name="search" value="<?php echo $this->search ?>" id="search" />
		<button type="submit"><?php echo JText::_('GO'); ?></button>
		</td>
		<td style="white-space:nowrap;">
			<?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>
	
<table class="adminlist">
  <thead>
    <tr class="sortable">
      <th style="width:20px;">
        <input type="checkbox" name="toggle" 
             value="" onclick="checkAll(<?php echo 
             count( $this->rows ); ?>);" />
      </th>      
      <th style="width:45px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'ID', 'id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
	  <th style="width:65px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Category', 'pid', $this->lists['order_Dir'], $this->lists['order']); ?></th>
	  <th style="width:150px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Thumbnail', 'thumb', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th style="width:200px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Name', 'name', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th style="text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Quicktake', 'quicktake', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th style="width:150px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Date Added', 'creation_date', $this->lists['order_Dir'], $this->lists['order']); ?></th>
      <th style="width:45px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Hits', 'hits', $this->lists['order_Dir'], $this->lists['order']); ?></th>
	  <th style="width:20px; text-align:center;" nowrap="nowrap"><?php echo JHTML::_( 'grid.sort', 'Published', 'published', $this->lists['order_Dir'], $this->lists['order']); ?></th>
	  <th style="width:110px; text-align:center;" nowrap="nowrap">
	  <?php echo JHTML::_('grid.sort',  'Order', 'ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	  <?php echo JHTML::_('grid.order',  $this->rows ); ?></th>
	  <th style="width:100px; text-align:center;"><?php echo JHTML::_( 'grid.sort', 'Access', 'access', $this->lists['order_Dir'], $this->lists['order']); ?></th>
    </tr>
  </thead>

  <?php
  jimport('joomla.filter.output');
  $k = 0;
  $m = count( $this->rows );
  for ($i = 0, $n = $m; $i < $n; $i++) {
    $row = &$this->rows[$i];
    $checked = JHTML::_('grid.id', $i, $row->id );
    $access = JHTML::_('grid.access', $row, $i);
    $published = JHTML::_('grid.published', $row, $i );
	$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&controller=categories&task=edit&id='. $row->id );
    ?>
    <tr class="<?php echo "row$k"; ?>">
      <td style="width:15px; text-align:center;">
        <?php echo $checked; ?>
      </td>      
      <td style="width:15px; text-align:center;">
        <?php echo $row->id; ?>
      </td>
      <td style="width:15px; text-align:center;">
        <?php echo $this->categories[$row->pid]; ?>
      </td>
      <td style="width:15px; text-align:center;">
        <?php if($row->thumb != '') { ?>
        <img src="<?php echo JURI::root(true).'/components/com_xgallery/helpers/img.php?file='.'/'.$row->thumb.'&w='.$this->rWidth.'&h='.$this->rHeight."&amp;tn=0"; ?>" style="border:none;" />
      	<?php } ?>
      </td>
      <td style="width:15px; text-align:center;">
        <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
      </td>
      <td>
        <?php echo $row->quicktake; ?>
      </td>
      <td style="width:15px; text-align:center;">
        <?php echo $row->creation_date; ?>
      </td>
      <td style="width:15px; text-align:center;">
        <?php echo $row->hits; ?>
      </td>
      <td style="width:15px; text-align:center;">
        <?php echo $published;?>
      </td>
      <td class="order" style="width:110px; text-align:center;">
      	<span><?php echo $this->pageNav->orderUpIcon( $i, true, 'orderup', 'Move Up', $this->lists['ordering']); ?></span>
		<span><?php echo $this->pageNav->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $this->lists['ordering'] ); ?></span>
		<?php $disabled = $this->lists['ordering'] ?  '' : 'disabled="disabled"'; ?>
		<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
	  </td>
      <td style="width:15px; text-align:center;">
        <?php echo $access;?>
      </td>
    </tr>
    <?php
    $k = 1 - $k;
  }
  ?>
<tfoot>
	<tr>
		<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
	</tr>
</tfoot>
</table>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="categories" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>