<?php
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/**
 * @version	1.5
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

if(!class_exists('plgCommunityScout'))
{
	class plgCommunityScout extends CApplications 
	{
		/**
		 * Constructor 
		 *
		 * For php4 compatability we must not use the __constructor as a constructor for plugins
		 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
		 * This causes problems with cross-referencing necessary for the observer design pattern.
		 *
		 * @param object $subject The object to observe
		 * @param 	array  $config  An array that holds the plugin configuration
		 * @since 1.5
		 */
		function plgCommunityScout(& $subject, $config) {
			parent::__construct($subject, $config);			
		}	
		
		/**
	     * Method to check if scout is installed
	     * @return boolean
	     */
	    function _isInstalled()
	    {
	        $success = false;
	        
	        jimport('joomla.filesystem.file');
	        if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'helpers'.DS.'_base.php')) 
	        {
	            $success = true;
	        }
	                
	        return $success;
	    }
		
		/**		 
		 * This event trigger when a new group is created.
		 * @param object $group
		 * 			id 			- the id of the group
		 * 			ownerid		- the user id of the group owner
		 * 			categoryid	- the category type id
		 * 			name		- name of the group.
		 * 			description	- the group description.
		 * 			email		- the group's email.
		 * 			website		- the group's website.
		 * 			created		- date when the group is created.
		 * 			approvals	- show whether the groups require approval or not when a user join (0 = no need aproval, 1 = requires approval).
		 * 			avatar		- the group's avatar location.
		 * 			thumb		- the group's thumbnail avatar location.
		 * 			published	- the publish status of the group (0 = unpublished, 1 = published).
		 * @param int $memberid	- User ID of the user who create the group.
		 */
		function onGroupCreate( &$group)
		{
			if (!plgCommunityScout::_isInstalled()) return;
        
			// get a scout logs object
        	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        	$log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
	        // set the subject
	        $log->setSubject(
	            array(
	                'value'=>JFactory::getUser()->id,		// required. the subject's unique identifier, generally a user id # 
	                'name'=>JFactory::getUser()->name,  	// required. the subject's name, generally a user's name or username.
	                'type'=>'user'                 			// optional. 'user' is the default
	            )
	        ); 
        
	        // set the verb's variables  
		    $verbValue = 'created';
		    $verbName = JText::_("Created");        	
	        
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
	        		return;
	        		break;
	        	case "0":
	        	default:
	        		$scope_url = 'index.php?option=com_community&view=groups&task=create';
	        		break;
	        }
        
        	$scope_name = JText::_("JomSocial Group");
	        // set the object
	        $log->setObject(
	            array(
	                'value'=>$group->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
	                'name'=>$group->name,                        // required. the object's plain english name. 
	                'scope_identifier'=>'com_community&view=groups', // required. is unique to this site+component+view(+layout) combo
	                'scope_name'=>$scope_name,       // optional. only necessary if this scope is a new one
	                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
	                'client_id'=>$client_id                         // optional. if missing, log object sets it.
	            )
	        );        
         
	        if (!$log->save())
	        {
	            JError::raiseNotice( 'plgCommunityScout01', "plgCommunityScout :: ". $log->getError() );
	        }
	
	        return true;
		}
		
		/**		 
		 * This event trigger when a user join a group.		  
		 * @param object $group
		 * 			id, the id of the group
		 * 			ownerid, the user id of the group owner
		 * 			categoryid, the category type id.
		 * 			name, name of the group.
		 * 			description, the group description.
		 * 			email, the group's email.
		 * 			website, the group's website.
		 * 			created, date when the group is created.
		 * 			approvals, show whether the groups require approval or not when a user join (0 = no need aproval, 1 = requires approval).
		 * 			avatar, the group's avatar location.
		 * 			thumb, the group's thumbnail avatar location.
		 * 			published, the publish status of the group (0 = unpublished, 1 = published).
		 * 
		 * @param int $memberid
		 */		
		function onGroupJoin( &$group, $memberid ) 
		{
  			if (!plgCommunityScout::_isInstalled()) return;
	        
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
		    $verbValue = 'joined';
		    $verbName = JText::_("Joined");        	
	        
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
	        		return;
	        		break;
	        	case "0":
	        	default:
	        		$scope_url = 'option=com_community&view=groups&task=viewgroup&groupid='.$group->id;
	        		break;
	        }
        
        	$scope_name = JText::_("JomSocial Group");
	        // set the object
	        $log->setObject(
	            array(
	                'value'=>$group->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
	                'name'=>$group->name,                        // required. the object's plain english name. 
	                'scope_identifier'=>'com_community&view=groups', // required. is unique to this site+component+view(+layout) combo
	                'scope_name'=>$scope_name,       // optional. only necessary if this scope is a new one
	                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
	                'client_id'=>$client_id                         // optional. if missing, log object sets it.
	            )
	        );        
         
	        if (!$log->save())
	        {
	            JError::raiseNotice( 'plgCommunityScout02', "plgCommunityScout :: ". $log->getError() );
	        }
	
	        return true;
		}
		/**
		 * This event is trigger after a group is deleted. 
		 * @param object $group
		 * 			id, the id of the group
		 * 			ownerid, the user id of the group owner
		 * 			categoryid, the category type id.
		 * 			name, name of the group.
		 * 			description, the group description.
		 * 			email, the group's email.
		 * 			website, the group's website.
		 * 			created, date when the group is created.
		 * 			approvals, show whether the groups require approval or not when a user join (0 = no need aproval, 1 = requires approval).
		 * 			avatar, the group's avatar location.
		 * 			thumb, the group's thumbnail avatar location.
		 * 			published, the publish status of the group (0 = unpublished, 1 = published).
		 * 			discusscount, the group's total discussions.
		 * 			wallcount, the group's total walls.
		 * 			membercount, the group's total members.
		 */
		function onAfterGroupDelete( $group ) 
		{
			
  			if (!plgCommunityScout::_isInstalled()) return;
	        
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
		    $verbName = JText::_("Deleted");        	
	        
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
	        		return;
	        		break;
	        	case "0":
	        	default:
	        		$scope_url = 'option=com_community&view=groups&task=viewgroup&groupid='.$group->id;
	        		break;
	        }
        
        	$scope_name = JText::_("JomSocial Group");
	        // set the object
	        $log->setObject(
	            array(
	                'value'=>$group->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
	                'name'=>$group->name,                        // required. the object's plain english name. 
	                'scope_identifier'=>'com_community&view=groups', // required. is unique to this site+component+view(+layout) combo
	                'scope_name'=>$scope_name,       // optional. only necessary if this scope is a new one
	                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
	                'client_id'=>$client_id                         // optional. if missing, log object sets it.
	            )
	        );        
         
	        if (!$log->save())
	        {
	            JError::raiseNotice( 'plgCommunityScout03', "plgCommunityScout :: ". $log->getError() );
	        }
	
	        return true;		  
		}
		
		/**		
		 * This event trigger when your profile avatar is updated.
		 * @param int $userid
		 * @param string $old_avatar_path
		 * @param string $new_avatar_path
		 */
		function onProfileAvatarUpdate( &$userid, &$old_avatar_path, &$new_avatar_path ) 
		{						
  			if (!plgCommunityScout::_isInstalled()) return;
  			  				        
			// get a scout logs object
        	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        	$log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
	        // set the subject
	        $log->setSubject(
	            array(
	                'value'=>$userid,   						// required. the subject's unique identifier, generally a user id # 
	                'name'=>JFactory::getUser($userid)->name,  	// required. the subject's name, generally a user's name or username.
	                'type'=>'user'                      		// optional. 'user' is the default
	            )
	        ); 
	        // set the verb's variables  
		    $verbValue = 'updated';
		    $verbName = JText::_("Updated");        	
	        
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
	        		return;
	        		break;
	        	case "0":
	        	default:
	        		$scope_url = 'index.php?option=com_community&view=profile&task=uploadAvatar';
	        		break;
	        }
        
        	$scope_name = JText::_("JomSocial Avatar");
	        // set the object
	        $log->setObject(
	            array(
	                'value'=>$userid,                          			// required. the object's unique identifier. (in the case of content article, is the article id #)
	                'name'=>JFactory::getUser($userid)->name,           // required. the object's plain english name. 
	                'scope_identifier'=>'com_community&view=profile&task=uploadAvatar', 	// required. is unique to this site+component+view(+layout) combo
	                'scope_name'=>$scope_name,       					// optional. only necessary if this scope is a new one
	                'scope_url'=>$scope_url,                        	// optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
	                'client_id'=>$client_id                         	// optional. if missing, log object sets it.
	            )
	        );        
         
	        if (!$log->save())
	        {
	            JError::raiseNotice( 'plgCommunityScout04', "plgCommunityScout :: ". $log->getError() );
	        }
	
	        return true;    
		}		
		
		/**		
		 * This event trigger after user profile data is saved. 
		 * @param int $userid - the userid of the profile that has been changed 
		 * @return boolean
		 */
		function onAfterProfileUpdate( $userid ) 
		{									
  			if (!plgCommunityScout::_isInstalled()) return;
	        
			// get a scout logs object
        	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        	$log = JTable::getInstance( 'Logs', 'ScoutTable' );
        
	        // set the subject
	        $log->setSubject(
	            array(
	                'value'=>$userid,   // required. the subject's unique identifier, generally a user id # 
	                'name'=>JFactory::getUser($userid)->name,  // required. the subject's name, generally a user's name or username.
	                'type'=>'user'                      // optional. 'user' is the default
	            )
	        ); 
	        
        
	        // set the verb's variables  
		    $verbValue = 'updated';
		    $verbName = JText::_("Updated");        	
	        
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
	        		$scope_url = 'index.php?option=com_community&view=users&layout=edit&id=';
	        		break;
	        	case "0":
	        	default:
	        		$scope_url = 'index.php?option=com_community&view=profile&task=edit';
	        		break;
	        }
        
        	$scope_name = JText::_("JomSocial Profile");
	        // set the object
	        $log->setObject(
	            array(
	                'value'=>$userid,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
	                'name'=>JFactory::getUser($userid)->name,                        // required. the object's plain english name. 
	                'scope_identifier'=>'com_community&view=profile', // required. is unique to this site+component+view(+layout) combo
	                'scope_name'=>$scope_name,       // optional. only necessary if this scope is a new one
	                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
	                'client_id'=>$client_id                         // optional. if missing, log object sets it.
	            )
	        );        
         
	        if (!$log->save())
	        {
	            JError::raiseNotice( 'plgCommunityScout05', "plgCommunityScout :: ". $log->getError() );
	        }
	
	        return true;    				
		}
	}
}