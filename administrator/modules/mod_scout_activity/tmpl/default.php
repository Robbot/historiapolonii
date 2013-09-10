<?php defined('_JEXEC') or die('Restricted Access'); ?>

<?php  // Add CSS
$document->addStyleSheet( JURI::root(true).'/administrator/modules/mod_scout_activity/tmpl/mod_scout_activity.css');
?>

<?php if ($items) : ?>
    <?php echo "<span id='scout_subheader'>".sprintf( JText::_( "LAST_X_ENTRIES" ), $limit )."</span>"; ?>
	<ul id="scout_activity">
		<?php foreach ($items as $item) : ?>
		<li class="scout_logitem">
			<?php
			echo "<span class='scout_subject'>".$item->subject."</span>";
			echo ' ';
			echo "<span class='scout_verb'>".strtolower( JText::_( $item->verb ) )."</span>";
			echo ' ';
			echo "<span class='scout_object'>";
    			echo JText::_( $item->object );
			echo "</span>";
            echo ' '.JText::_("in").' ';
			echo '<br/>';
			echo "<span class='scout_scopename'>";
			    if (!empty($item->scope_url)) :
			        echo "<a href='".$item->scope_url."'>";
			    endif;
			     
			    echo JText::_( $item->scope_name );

                if (!empty($item->scope_url)) :
                    echo "</a>";
                endif;
			echo "</span>";
            echo ' '.strtolower(JText::_("on")).' ';
            echo "<span class='scout_datetime'>";
                echo JHTML::_( 'date', $item->datetime, '%a, %d %b %Y, %I:%M%p' );
            echo "</span>";
			?>
		</li>
		<?php endforeach; ?>
	</ul>
    <a id="scout_view_all" href="<?php echo JRoute::_( "index.php?option=com_scout&view=logs&filter_order=tbl.datetime&filter_direction=DESC" ); ?>"><?php echo JText::_( "View All Activity" ); ?></a>
<?php else : ?>
	<? echo JText::_('Nothing to Show'); ?>
<?php endif; ?>
