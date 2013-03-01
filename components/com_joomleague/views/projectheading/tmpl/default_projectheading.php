<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$nbcols = 2;
if ( $this->overallconfig['show_project_picture'] ) { $nbcols++; }

if ( $this->overallconfig['show_project_heading'] == "1" && $this->project)
{
	?>
	<div class="componentheading">
		<table class="contentpaneopen">
			<tbody>
				<?PHP
				if ( $this->overallconfig['show_project_country'] == "1" )
				{
					?>
				<tr class="contentheading">
					<td colspan="<?php echo $nbcols; ?>">
					<?php
					$country = $this->project->country;
					echo Countries::getCountryFlag($country) . ' ' . Countries::getCountryName($country);
					?>
					</td>
				</tr>
				<?php	
			   	}
				?>
				<tr class="contentheading">
					<?php	
			    	if ( $this->overallconfig['show_project_picture'] == "1" )
					{
						?>
						<td>
						<?php
						echo JoomleagueHelper::getPictureThumb($this->project->picture,
																$this->project->name,
																$this->overallconfig['picture_width'],
																$this->overallconfig['picture_height'], 
																2);
						?>
						</td>
					<?php	
			    	}
			    	?>
					<td>
					<?php
					echo $this->project->name;
					if (isset( $this->division))
					{
						echo ' - ' . $this->division->name;
					}
					?>
					</td>
					<td class="buttonheading" align="right">
					<?php
					if(JRequest::getVar('print') != 1) {
						echo JoomleagueHelper::printbutton(null, $this->overallconfig);
					}
					?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php 
}
?>