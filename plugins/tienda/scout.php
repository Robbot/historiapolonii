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

class plgTiendaScout extends JPlugin
{
    function plgTiendaScout( &$subject, $params )
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
     * Tracks when users add items to their cart
     * 
     * @param $item     Cart Item
     * @param $values   Post Values
     * @return unknown_type
     */
    function onAfterAddToCart( $item, $values )
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
        $verbValue = 'added';
        $verbName = 'Added';

        // set the verb
        $log->setVerb(
            array(
                'value'=>$verbValue,    // required. unique identifier for this action
                'name'=>$verbName       // optional. if this is a new verb, this is the plain English name for it
            )
        );

        $product = JTable::getInstance('Products', 'TiendaTable');
        $product->load( array( 'product_id'=>$item->product_id ) );
        
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
        		$scope_url = 'index.php?option=com_tienda&view=products&task=view&id=';
        		break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$item->product_id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$product->product_name,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_tienda&view=products', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Tienda Products',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgTiendaScout01', "plgTiendaScout :: ". $log->getError() );
        }

        return true;
    }

    /**
     * Tracks when users view items
     * 
     * @param $item     Cart Item
     * @param $values   Post Values
     * @return unknown_type
     */
    function onAfterDisplayProduct( $product_id )
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

        $product = JTable::getInstance('Products', 'TiendaTable');
        $product->load( array( 'product_id'=>$product_id ) );
        
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
                $scope_url = 'index.php?option=com_tienda&view=products&task=view&id=';
                break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$product_id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$product->product_name,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_tienda&view=products', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Tienda Products',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgTiendaScout02', "plgTiendaScout :: ". $log->getError() );
        }

        return true;
    }

    /**
     * Tracks when users view items
     * 
     * @param $item     Cart Item
     * @param $values   Post Values
     * @return unknown_type
     */
    function onAfterSaveProducts( $item )
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

        $product = JTable::getInstance('Products', 'TiendaTable');
        $product->load( array( 'product_id'=>$item->product_id ) );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
            case "1":
                $scope_url = 'index.php?option=com_tienda&view=products&task=edit&id=';
                break;
            case "0":
            default:
                $scope_url = 'index.php?option=com_tienda&view=products&task=view&id=';
                break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$product->product_id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$product->product_name,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_tienda&view=products', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Tienda Products',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgTiendaScout03', "plgTiendaScout :: ". $log->getError() );
        }

        return true;
    }
    
    /**
     * Tracks when users view items
     * 
     * @param $item     Cart Item
     * @param $values   Post Values
     * @return unknown_type
     */
    function onAfterSaveCategories( $item )
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

        $category = JTable::getInstance('Categories', 'TiendaTable');
        $category->load( array( 'category_id'=>$item->category_id ) );
        
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
            case "1":
                $scope_url = 'index.php?option=com_tienda&view=categories&task=edit&id=';
                break;
            case "0":
            default:
                return;
                break;
        }
        // set the object
        $log->setObject(
            array(
                'value'=>$category->category_id,                          // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$category->category_name,                      // required. the object's plain english name. 
                'scope_identifier'=>'com_tienda&view=categories', // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Tienda Categories',       // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            )
        );        
         
        if (!$log->save())
        {
            JError::raiseNotice( 'plgTiendaScout04', "plgTiendaScout :: ". $log->getError() );
        }

        return true;
    }
}
