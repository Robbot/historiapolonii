<?php
/**
 * @package Scout
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgAmbrasubsScout extends JPlugin
{
    function plgAmbrasubsScout( &$subject, $params )
    {
        parent::__construct( $subject, $params );
    }
    
    /**
     * Checks if Scout is installed
     * @return unknown_type
     */
    function isInstalled()
    {
        $success = false;

        jimport( 'joomla.filesystem.file' );
        $filePath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'scout.php';
        if (JFile::exists($filePath))
        {
            $success = true;
        }
        return $success;
    }

    /**
     * Tracks when users create subscription
     * 
     * @param $item     object Item
     * @return unknown_type
     */
    function onAfterSaveSuccessType(  $data, $user, $msg )
    {
        if (!$this->isInstalled())
        {
            return;
        }

        $user_id = JFactory::getUser()->id;
        if (empty($user_id))
        {
            return;
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
        
        $isNew = !JRequest::getInt('id');
        // set the verb's variables
        if($isNew)
        {
	        $verbValue = 'created';
	        $verbName = 'Created';
        }
        else
        {
        	$verbValue = 'edited';
	        $verbName = 'Edited';
        }
        
        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
        	case "1":
        		$scope_url = 'index.php?option=com_ambrasubs&view=types&task=view&id=';
        		break;
        	case "0":
        	default:        		
        		break;
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>$data->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$data->title,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_ambrasubs&view=types', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'AS Subscription Types',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgAmbrasubsScout01', "plgAmbrasubsScout :: ". $log->getError() );
        }

        return true;
    }
    
    function onAfterDeleteType( $arg )
    {
    	if (!$this->isInstalled())
        {
            return;
        }
        
        $user_id = JFactory::getUser()->id;
        if (empty($user_id))
        {
            return;
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
        
        // set the verb's variables
        $verbValue = 'deleted';
        $verbName = 'Deleted';

        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
        	case "1":
        		$scope_url = 'index.php?option=com_ambrasubs&view=types&task=view&id=';
        		break;
        	case "0":
        	default:        		
        		break;
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>$arg->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$arg->title,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_ambrasubs&view=types', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'AS Subscription Types',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgAmbrasubsScout01', "plgAmbrasubsScout :: ". $log->getError() );
        }

        return true;
    }
    
    function onAfterDisplayComponentAmbrasubs()
    {
    	if (!$this->isInstalled())
        {
            return;
        }
        
        $user_id = JFactory::getUser()->id;
        if (empty($user_id))
        {
            return;
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
        
        // set the verb's variables
        $verbValue = 'viewed';
        $verbName = 'Viewed';

        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
        	case "1":        		
        		break;
        	case "0":
        	default: 
        		$scope_url = 'index.php?option=com_ambrasubs&view=subscriptions&Itemid=';       		
        		break;
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>'Subscription Types',                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>'Subscription Types',                      // required. the object's plain english name. 
                'scope_identifier'=>'com_ambrasubs&view=types', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'AS Subscription Types',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgAmbrasubsScout01', "plgAmbrasubsScout :: ". $log->getError() );
        }

        return true;
    }
    
	function onAfterSaveSuccessFile( $data, $user, $msg )
    {
        if (!$this->isInstalled())
        {
            return;
        }
        
        $user_id = JFactory::getUser()->id;
        if (empty($user_id))
        {
            return;
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
        
        $isNew = !JRequest::getInt('id');
        // set the verb's variables
        if($isNew)
        {
	        $verbValue = 'created';
	        $verbName = 'Created';
        }
        else
        {
        	$verbValue = 'edited';
	        $verbName = 'Edited';
        }
        
        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
        	case "1":
        		$scope_url = 'index.php?option=com_ambrasubs&view=files&task=view&id=';
        		break;
        	case "0":
        	default:        		
        		break;
        }
                
        // set the object
        $log->setObject(
            array(
                'value'=>$data->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$data->filename,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_ambrasubs&view=files', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'AS Files',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgAmbrasubsScout01', "plgAmbrasubsScout :: ". $log->getError() );
        }

        return true;
    }
    
	function onAfterDeleteFile( $arg )
    {
    	if (!$this->isInstalled())
        {
            return;
        }
        
        $user_id = JFactory::getUser()->id;
        if (empty($user_id))
        {
            return;
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
        
        // set the verb's variables
        $verbValue = 'deleted';
        $verbName = 'Deleted';

        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
        	case "1":
        		$scope_url = 'index.php?option=com_ambrasubs&view=files&task=view&id=';
        		break;
        	case "0":
        	default:        		
        		break;
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>$arg->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$arg->filename,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_ambrasubs&view=files', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'AS Files',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgAmbrasubsScout01', "plgAmbrasubsScout :: ". $log->getError() );
        }

        return true;
    }
}
