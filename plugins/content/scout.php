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

class plgContentScout extends JPlugin
{
    function plgContentScout( &$subject, $params )
    {
        parent::__construct( $subject, $params );
    }

    /**
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param   object      A JTableContent object
     * @param   bool        If the content is just about to be created
     * @return  void
     */
    function onAfterContentSave( &$article, $isNew )
    {
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
        $verbValue = 'modified';
        $verbName = 'Modified';
        if ($isNew) { 
	        $verbValue = 'created';
	        $verbName = 'Created';        	
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
        		$scope_url = 'index.php?option=com_content&view=article&task=edit&cid[]=';
        		break;
        	case "0":
        	default:
        		$scope_url = 'index.php?option=com_content&view=article&task=edit&id=';
        		break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$article->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$article->title,                        // required. the object's plain english name. 
                'scope_identifier'=>'com_content&view=article', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Content Manager',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgContentScout01', "plgContentScout :: ". $log->getError() );
        }

        return true;
    }

}
