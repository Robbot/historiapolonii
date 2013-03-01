<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewTeamInfo extends JLGView
{
	function display( $tpl = null )
	{
		// Get a refrence of the page instance in joomla
		$document= & JFactory::getDocument();
		$model = & $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		$paramsdata = '';
		
		$this->assignRef( 'project', $model->getProject() );
		$this->assignRef( 'overallconfig', $model->getOverallConfig() );
		$this->assignRef( 'config', $config );

		if ( isset($this->project->id) )
		{
			$team = $model->getTeamByProject();
			$this->assignRef( 'team',  $team);
			$this->assignRef( 'club', $model->getClub() );
			$this->assignRef( 'seasons', $model->getSeasons( $config ) );
			$paramsdata = $team->teamextended;
		}
		$paramsdefs = JLG_PATH_ADMIN . DS . 'assets' . DS . 'extended' . DS . 'team.xml';
		$extended = new JLGExtraParams( $paramsdata, $paramsdefs );
		$this->assignRef( 'extended', $extended );
		// Set page title
		$pageTitle = JText::_( 'JL_TEAMINFO_PAGE_TITLE' );
		if ( isset( $this->team ) )
		{
			$pageTitle .= ': ' . $this->team->tname;
		}
		$document->setTitle( $pageTitle );

		parent::display( $tpl );
	}
}
?>