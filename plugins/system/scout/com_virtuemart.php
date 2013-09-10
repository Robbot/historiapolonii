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

class plgScoutComVirtuemart extends JObject 
{
	var $_loadItem = false;
	
    function plgScoutComVirtuemart()
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
        	JError::raiseNotice( 'plgScoutComVirtuemart01', "plgScoutComVirtuemart :: ". $log->getError() );
        }
        

        return true;
    }
    
    /**
     * Based on the $page, $product_id variables, 
     * sets the verb array's properties
     * 
     * @return array
     */
    function getVerb()
    {
    	$return = array();
    	
    	$page = JRequest::getVar('page');
    	$product_id = (int) JRequest::getVar('product_id');

    	switch ($page)
    	{
    		case "shop.product_details":
                $return['value'] = 'viewed';
                $return['name'] = 'Viewed';	 
                $this->_loadItem = true;   
    			break;
            default:
            	return false; // unsupported page
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
                $scope_url = 'index.php?option=com_virtuemart&page=shop.product_details&product_id=';
                break;
        }

        $product_id = (int) JRequest::getVar('product_id');
        if (!empty($this->_loadItem))
        {
            $db = JFactory::getDBO();
            $db->setQuery( "SELECT * FROM #__vm_product WHERE `product_id` = '$product_id'; " );
            $result = $db->loadObject();
            $name = $result->product_name;
        }

        $return =
            array(
                'value'=>$product_id,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$name,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_virtuemart&page=shop.product_details',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Virtuemart Product',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;
    	
    }
}