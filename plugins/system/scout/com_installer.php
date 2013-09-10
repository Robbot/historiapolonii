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

class plgScoutComInstaller extends JObject 
{
    var $_loadItem = false;
    
    function plgScoutComInstaller()
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
            JError::raiseNotice( 'plgScoutComInstaller01', "plgScoutComInstaller :: ". $log->getError() );
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
            case "remove":
                $return['value'] = 'uninstalled';
                $return['name'] = 'Uninstalled';
                $this->_loadItem = true;
                break;
            case "disable":
                $return['value'] = 'disabled';
                $return['name'] = 'Disabled';
                $this->_loadItem = true;
                break;
            case "enable":
                $return['value'] = 'enabled';
                $return['name'] = 'Enabled';
                $this->_loadItem = true;
                break;
            case "doInstall":
            case "doinstall":
                $return['value'] = 'installed';
                $return['name'] = 'Installed';
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
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
            case "1": // admin
                $scope_url = 'index.php?option=com_installer';
                break;
            case "0": // site
            default:
                return false; // don't do anything for the site
                break;
        }
        
        // get the id of item being deleted or pub/unpub
        $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
        $array = JRequest::getVar('eid', array( $id ), 'request', 'array');
        if (empty($array[0]))
        {
        	// TODO Is there any way to get more descriptive information about the package being installed?
        	$value = 'new';
        	$title = 'New Extension';
        } 
            else
        {
            $value = $array[0];
        }
        
        if (!empty($this->_loadItem) && is_numeric($value)) 
        {
        	$type  = JRequest::getWord('type', 'components');
        	switch ($type)
        	{
                case "modules":
                case "module":
                	$row =& JTable::getInstance('module');
                    $row->load($value);
                    $title = $row->title;
                    break;
        		case "plugins":
                case "plugin":
                	$row =& JTable::getInstance('plugin');
                    $row->load($value);
                    $title = $row->name;
                    break;
        		case "components":
        	    case "component":
        	    default:
        	    	$row =& JTable::getInstance('component');
		            $row->load($value);
		            $title = $row->name;		        	    	
        			break;
        	}
//        	$scope_url .= '&type='.$type;
        }
        
        $return =
            array(
                'value'=>$value,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_installer&view=item',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Extension Manager',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;
        
    }
}