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

class plgScoutComKunena extends JObject 
{
	var $_loadItem = false;
	
    function plgScoutComKunena()
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
        	JError::raiseNotice( 'plgScoutComKunena01', "plgScoutComKunena :: ". $log->getError() );
        }
        

        return true;
    }
    
    /**
     * Based on the $func, $action, and $parentid variables, 
     * sets the verb array's properties
     * 
     * @return array
     */
    function getVerb()
    {
    	$return = array();
    	
    	$action = JRequest::getVar('action');
    	$parentid = (int) JRequest::getVar('parentid');
    	$func = JRequest::getVar('func');
    	switch ($func)
    	{
    		case "post":
    		    switch ($action)
    		    {
    		        case "post":
                        $return['value'] = 'replied';
                        $return['name'] = 'Replied';
                        if (empty($parentid))
                        {
                            $return['value'] = 'created';
                            $return['name'] = 'Created';
                            $this->_loadItem = true;
                        }
    		            break;
    		        default:
    		            return false; // unsupported action
                        break;
    		    }
    			break;
            case "view":
                return false; // unsupported func for now
                // $return['value'] = 'viewed';
                // $return['name'] = 'Viewed';
                break;
            default:
            	return false; // unsupported func
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
                return false; // don't do anything for the admin w/ com_kunena
                break;
            case "0": // site
            default:
                $scope_url = 'index.php?option=com_kunena&func=view&id=';
                break;
        }

        $subject = JRequest::getVar('subject');
        $parentid = (int) JRequest::getVar('parentid');
        if (!empty($this->_loadItem))
        {
            // get theid number of the thread. Since it's a new one, the id isn't in the REQUEST
            $db = JFactory::getDBO();
            $tbl = $db->replacePrefix( '#__fb_messages' );
            $db->setQuery( "SHOW TABLE STATUS LIKE '$tbl'" );
            $result = $db->loadAssoc();
            $parentid = $result['Auto_increment'];
        }

        $return =
            array(
                'value'=>$parentid,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$subject,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_kunena&view=post',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Kunena Forum Post',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;
    	
    }
}