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

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgUserScout extends JPlugin
{
    function plgUserScout( &$subject, $params )
    {
        parent::__construct( $subject, $params );
    }

    /**
     * Once completed, will track user edits
     *
     * Method is called after user data is stored in the database
     *
     * @param   array       holds the new user data
     * @param   boolean     true if a new user is stored
     * @param   boolean     true if user was succesfully stored in the database
     * @param   string      message
     */
    function onAfterStoreUser($user, $isnew, $success, $msg)
    {
        
    }

    /**
     * Once completed, will track user deletions
     *
     * Method is called after user data is deleted from the database
     *
     * @param   array       holds the user data
     * @param   boolean     true if user was succesfully stored in the database
     * @param   string      message
     */
    function onAfterDeleteUser($user, $succes, $msg)
    {
        // only the $user['id'] exists and carries valid information

        // Call a function in the external app to delete the user
        // ThirdPartyApp::deleteUser($user['id']);
    }

    /**
     * Tracks user logins
     *
     * @access  public
     * @param   array   holds the user data
     * @param   array    extra options
     * @return  boolean True on success
     * @since   1.5
     */
    function onLoginUser($user, $options)
    {
        jimport('joomla.user.helper');
        $user['id'] = intval(JUserHelper::getUserId($user['username']));
        
        // get a scout logs object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
        // set the subject
        $log->setSubject(
            array(
                'value'=>$user['id'],   // required. the subject's unique identifier, generally a user id # 
                'name'=>JFactory::getUser($user['id'])->name,  // required. the subject's name, generally a user's name or username.
                'type'=>'user'                      // optional. 'user' is the default
            )
        ); 
        
        // set the verb's variables
        $verbValue = 'login';
        $verbName = 'Logged In';
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
                $scope_url = 'index.php?option=com_users&view=user&task=edit&cid[]=';
                break;
            case "0":
            default:
                $scope_url = 'index.php?option=com_user&view=user&id=';
                break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$user['id'],                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>JFactory::getUser($user['id'])->name,                        // required. the object's plain english name. 
                'scope_identifier'=>'com_user&view=user', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'User Manager',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgUserScout01', "plgUserScout :: ". $log->getError() );
        }

        return true;
    }

    /**
     * Tracks user logouts
     *
     * @access public
     * @param array holds the user data
     * @return boolean True on success
     * @since 1.5
     */
    function onLogoutUser($user)
    {
        jimport('joomla.user.helper');
        $user['id'] = intval(JUserHelper::getUserId($user['username']));
        
        // get a scout logs object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
        // set the subject
        $log->setSubject(
            array(
                'value'=>$user['id'],   // required. the subject's unique identifier, generally a user id # 
                'name'=>JFactory::getUser($user['id'])->name,  // required. the subject's name, generally a user's name or username.
                'type'=>'user'                      // optional. 'user' is the default
            )
        ); 
        
        // set the verb's variables
        $verbValue = 'logout';
        $verbName = 'Logged Out';
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
                $scope_url = 'index.php?option=com_users&view=user&task=edit&cid[]=';
                break;
            case "0":
            default:
                $scope_url = 'index.php?option=com_user&view=user&id=';
                break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$user['id'],                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>JFactory::getUser($user['id'])->name,                        // required. the object's plain english name. 
                'scope_identifier'=>'com_user&view=user', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'User Manager',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgUserScout01', "plgUserScout :: ". $log->getError() );
        }

        return true;
    }

}
