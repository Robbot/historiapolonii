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

class plgScoutComMenus extends JObject 
{
	var $_loadItem = false;
	
    function plgScoutComMenus()
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
        	JError::raiseNotice( 'plgScoutComMenus01', "plgScoutComMenus :: ". $log->getError() );
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
    		case "apply":
                $return['value'] = 'modified';
                $return['name'] = 'Modified';
	            $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
	            $array = JRequest::getVar('cid', array( $id ), 'post', 'array');
	            if (empty($array[0]))
	            {
	                $return['value'] = 'created';
	                $return['name'] = 'Created';
	            }
    			break;
            case "remove":
                $return['value'] = 'deleted';
                $return['name'] = 'Deleted';
                $this->_loadItem = true;
                break;
            case "unpublish":
                $return['value'] = 'unpublished';
                $return['name'] = 'Unpublished';
                $this->_loadItem = true;
                break;
            case "publish":
                $return['value'] = 'published';
                $return['name'] = 'Published';
                $this->_loadItem = true;
                break;
            case "orderup":
            case "orderdown":
            case "reorder":
                $return['value'] = 'reordered';
                $return['name'] = 'ReOrdered';
                $this->_loadItem = true;
                break;
            case "accesspublic":
            case "accessregistered":
            case "accessspecial":
                $return['value'] = 'changedacl';
                $return['name'] = 'Changed ACL';
                $this->_loadItem = true;
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
                $scope_url = 'index.php?option=com_menus&task=edit&cid[]=';
                break;
            case "0": // site
            default:
                return false; // don't do anything for the site w/ com_modules
                break;
        }
        
        // get the id of item being edited or delteed or pub/unpub
        $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
        $array = JRequest::getVar('cid', array( $id ), 'post', 'array');
        if (empty($array[0]))
        {
	        // TODO something needs to be done about this - how can we get the id number?  is it even possible?
	        // this system plugin executes before the save occurs.
	        // is it logical to query the DB table for the next autoincrement id?  what if the save fails?
	        $db = JFactory::getDBO();
	        $tbl = $db->replacePrefix( '#__menu' );
	        $db->setQuery( "SHOW TABLE STATUS LIKE '$tbl'" );
	        $result = $db->loadAssoc();
	        $value = $result['Auto_increment'];
	        $title = JRequest::getVar('name');
        } 
            else
        {
	        $value = $array[0];
	        $title = JRequest::getVar('name');
        }
        
        if ($this->_loadItem && is_numeric($value)) 
        {
        	$row =& JTable::getInstance('menu');
        	$row->load($value);
        	$title = $row->name;
        }

        $return =
            array(
                'value'=>$value,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_menus&view=item',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Menu Manager',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;
    	
    }
}