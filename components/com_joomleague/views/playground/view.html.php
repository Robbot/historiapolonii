<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class JoomleagueViewPlayground extends JLGView
{
    function display( $tpl = null )
    {
        // Get a refrence of the page instance in joomla
        $document= & JFactory::getDocument();

        // Set page title
        $document->setTitle( JText::_( 'JL_PLAYGROUND_TITLE' ) );

        $model = & $this->getModel();
        $config = $model->getTemplateConfig($this->getName());

        $this->assignRef( 'project', $model->getProject() );
        $this->assignRef( 'overallconfig', $model->getOverallConfig() );
        $this->assignRef( 'config', $config );

        $model = $this->getModel( 'playground' );
        $games = $model->getNextGames();
        $gamesteams = $model->getTeamsFromMatches( $games );
        $this->assignRef( 'playground',  $model->getPlayground() );
        $this->assignRef( 'teams', $model->getTeams() );
        $this->assignRef( 'games', $games );
        $this->assignRef( 'gamesteams', $gamesteams );

        $this->assignRef( 'mapconfig', $model->getMapConfig() );
        $this->assignRef( 'address_string', $model->getAddressString() );

        $this->assignRef( 'gmap', $model->getGoogleMap( $this->mapconfig, $this->address_string ) );
        // $gm = $this->getModel( 'googlemap' );
        // $this->assignRef('gm', $gm->getGoogleMap( $model->getMapConfig(), $model->getAddressString() ) );

		$paramsdata = $this->playground->extended;
		$paramsdefs = JLG_PATH_ADMIN . DS . 'assets' . DS . 'extended' . DS . 'playground.xml';
		$extended = new JLGExtraParams( $paramsdata, $paramsdefs );

    	$this->assignRef( 'extended', $extended );		
		
        // Set page title
        $pageTitle = JText::_( 'JL_PLAYGROUND_PAGE_TITLE' );
        if ( isset( $this->playground->name ) )
        {
			$pageTitle .= ' - ' . $this->playground->name;
        }
        $document->setTitle( $pageTitle );
        $document->addCustomTag( '<meta property="og:title" content="' . $this->playground->name .'"/>' );
        $document->addCustomTag( '<meta property="og:street-address" content="' . $this->address_string .'"/>' );
        parent::display( $tpl );
    }
}
?>