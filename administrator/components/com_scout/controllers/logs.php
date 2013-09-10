<?php
/**
 * @version	1.5
 * @package	Scout
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class ScoutControllerLogs extends ScoutController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'logs');
	}
	
    /**
     * Sets the model's default state based on values in the request
     *
     * @return array()
     */
    function _setModelState()
    {
    	$state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

        $state = array();

        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.datetime', 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'DESC', 'word');
        $state['filter_date_from']  = $app->getUserStateFromRequest($ns.'date_from', 'filter_date_from', '', '');
        $state['filter_date_to']    = $app->getUserStateFromRequest($ns.'date_to', 'filter_date_to', '', '');
        $state['filter_subject']       = $app->getUserStateFromRequest($ns.'subject', 'filter_subject', '', '');
        $state['filter_verb']       = $app->getUserStateFromRequest($ns.'verb', 'filter_verb', '', '');
        $state['filter_object']       = $app->getUserStateFromRequest($ns.'object', 'filter_object', '', '');
        $state['filter_scopeid']       = $app->getUserStateFromRequest($ns.'scopeid', 'filter_scopeid', '', '');
        $state['filter_subjectid']       = $app->getUserStateFromRequest($ns.'subjectid', 'filter_subjectid', '', '');
        $state['filter_verbid']       = $app->getUserStateFromRequest($ns.'verbid', 'filter_verbid', '', '');
        $state['filter_objectid']       = $app->getUserStateFromRequest($ns.'objectid', 'filter_objectid', '', '');
        $state['filter_client']       = $app->getUserStateFromRequest($ns.'client', 'filter_client', '', '');
        $state['filter_scope']       = $app->getUserStateFromRequest($ns.'scope', 'filter_scope', '', '');
        
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }
}

?>