<?php
/**
 * @version 1.5
 * @package Scout
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class plgScoutComConfig extends JObject 
{
	var $_loadItem = false;
	
    function plgScoutComConfig()
    {
        parent::__construct();
    }
    
    /**
     * Function creates a logs entry for current page
     * 
     * @return null
     */
    function createLogEntry()
    {
        // get the verb if possible 
        if (!$verb = $this->getVerb())
        {
            // don't do anything
            return false;
        }
        
        // get the object if possible 
        if (!$object = $this->getObject())
        {
            // don't do anything
            return false;
        }
        
        // get a scout logs object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
        // set the subject
        $log->setSubject(
            array(
                'value'=>JFactory::getUser()->id,   // required. the subject's unique identifier, generally a user id # 
                'name'=>JFactory::getUser()->name,  // required. the subject's name, generally a user's name or username.
                'type'=>'user'                      // optional. 'user' is the default
            )
        ); 

        // set the verb
        $log->setVerb( $verb );
        
        // set the object
        $log->setObject( $object );
        
        if (!$log->save())
        {
        	JError::raiseNotice( 'plgScoutComConfig01', "plgScoutComConfig :: ". $log->getError() );
        }
        

        return true;
    }
    
    /**
     * Based on the $task variable, sets the verb array's properties
     * 
     * @return array
     */
    function getVerb()
    {
    	$return = array();
    	
    	$task = JRequest::getVar('task');
    	switch ($task)
    	{
    		case "save":
                $return['value'] = 'modified';
                $return['name'] = 'Modified';
    			break;
            default:
            	return false; // invalid task
                break;
    	}
    
    	return $return;
    }
    
    /**
     * Based on the post, sets the object array's properties
     * 
     * @return array
     */
    function getObject()
    {
    	$controller = strtolower( JRequest::getWord('controller', 'application') );
    	$component = strtolower( JRequest::getCmd( 'component' ) );
    	$component_string = $component ? "&component=$component" : "";
    	$component_name = $component ? $component : "Joomla";
    	
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
            case "1": // admin
                $scope_url = 'index.php?option=com_config&task=edit&controller='.$controller.$component_string;
                break;
            case "0": // site
            default:
                return false; // don't do anything for the site w/ com_config
                break;
        }

        // set the object array
        $return =
            array(
                'value'=>strtolower($component_name),                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>'Configuration of '.$component_name,                        // required. the object's plain english name. 
                'scope_identifier'=>'com_config&controller='.$controller.$component_string,  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Configuration of '.$component_name,        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;
    	
    }
}