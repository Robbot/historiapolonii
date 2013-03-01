<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="no-column">
	<div class="contentpaneopen">
		<div class="contentheading">
			<?php echo JText::_('JL_CLUBINFO_TEAMS'); ?>
		</div>
	</div>
	<div class="left-column-teamlist">
	<?php
		foreach ( $this->teams as $team )
		{
			if ( isset($team->team_name) )
			{
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $team->pid, $team->slug );
				?>
				<span class="clubinfo_team_item">
					<?php
						echo JHTML::link( $link, $team->team_name );
						echo "&nbsp;";
						if ( $team->team_shortcut ) { echo "(" . $team->team_shortcut . ")"; }
					?>
				</span>
				<span class="clubinfo_team_value">
				<?php
				if ( $team->team_description )
				{
					echo $team->team_description;
				}
				else
				{
					echo "&nbsp;";
				}
				?>
				</span>
				<?php
			}
		}
	?>
	</div>
</div>