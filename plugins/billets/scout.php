<?php
/**
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

class plgBilletsScout extends JPlugin
{
    function plgBilletsScout( &$subject, $params )
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
     * Tracks when users add comment to ticket
     * 
     * @param $item     object Item
     * @return unknown_type
     */
    function onAfterSaveComment( $item )
    {
        if (!$this->isInstalled())
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
        $verbValue = 'commented';
        $verbName = 'Commented';

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
        		$scope_url = 'index.php?option=com_billets&view=tickets&task=view&id=';
        		break;
        	case "0":
        	default:
        		$scope_url = 'index.php?option=com_billets&view=tickets&task=view&id=';
        		break;
        }
        
        $title = $item->title;
        JLoader::import( 'com_billets.helpers.category', JPATH_ADMINISTRATOR.DS.'components' );
        $cat_title = BilletsHelperCategory::getTitle( $item->categoryid, 'flat' );
        if (!empty($cat_title))
        {
            $title .= "\n[" . $cat_title . "]"; 
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>$item->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_billets&view=tickets', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Billets Tickets',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgBilletsScout01', "plgBilletsScout :: ". $log->getError() );
        }

        return true;
    }

    /**
     * Tracks when users create & edit tickets
     * 
     * @param $item     object Item
     * @return unknown_type
     */
    function onAfterSaveTickets( $item )
    {
        if (!$this->isInstalled())
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
        $verbValue = 'modified';
        $verbName = 'Modified';
        if (!empty($item->_isNew))
        {
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
                $scope_url = 'index.php?option=com_billets&view=tickets&task=view&id=';
                break;
            case "0":
            default:
                $scope_url = 'index.php?option=com_billets&view=tickets&task=view&id=';
                break;
        }
        
        $title = $item->title;
        JLoader::import( 'com_billets.helpers.category', JPATH_ADMINISTRATOR.DS.'components' );
        $cat_title = BilletsHelperCategory::getTitle( $item->categoryid, 'flat' );
        if (!empty($cat_title))
        {
            $title .= "\n[" . $cat_title . "]"; 
        }
        
        // set the object
        $log->setObject(
            array(
                'value'=>$item->id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_billets&view=tickets', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Billets Tickets',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgBilletsScout02', "plgBilletsScout :: ". $log->getError() );
        }

        return true;
    }
}
