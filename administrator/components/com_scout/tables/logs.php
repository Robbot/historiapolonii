<?php
/**
 * @version	1.5
 * @package	Scout
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'com_scout.tables._base', JPATH_ADMINISTRATOR.DS.'components' );

class ScoutTableLogs extends ScoutTable 
{
	function ScoutTableLogs( &$db ) 
	{
		
		$tbl_key 	= 'log_id';
		$tbl_suffix = 'logs';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'scout';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	/**
	 * Checks the object's integrity before storing to the DB
	 * 
	 * @return unknown_type
	 */
	function check()
	{
	    $db         = $this->getDBO();
        $nullDate   = $db->getNullDate();
        if (empty($this->datetime) || $this->datetime == $nullDate)
        {
            $date = JFactory::getDate();
            $this->datetime = $date->toMysql();
        }
        
		if (empty($this->subject_id))
		{
			$this->setError( JText::_( "Subject Required" ) );
			return false;
		}
		
	    if (empty($this->verb_id))
        {
            $this->setError( JText::_( "Verb Required" ) );
            return false;
        }

        if (empty($this->object_id))
        {
            $this->setError( JText::_( "Object Required" ) );
            return false;
        }
		return true;
	}
	
	/**
	 * Given a named array with values, 
	 * will intelligently set the log object's properties
	 *  
	 * @param array $array
	 * @return boolean
	 */
	function setObject( $array )
	{
		// using these:
//                'value'=>'22',                                  // required. the object's unique identifier. (in the case of content article, is the article id #)
//                'name'=>'What\'s New in 1.5?',                  // required. the object's plain english name. 
//                'scope_identifier'=>'com_content&view=article', // required. is unique to this site+component+view(+layout) combo
//                'scope_name'=>'The Core Content Manager',       // optional. only necessary if this scope is a new one
//                'scope_url'=>'index.php?option=com_content&view=article&task=edit&cid[]=',  // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
//                'client_id'=>$client_id                            // optional. if missing, defaults to front-end (0). admin-side = '1';

        // check that client_id is set
        $valid_clients = array('0', '1');
        if (empty($array['client_id']) || !in_array( (int) $array['client_id'], $valid_clients))
        {
	        $app = JFactory::getApplication();
	        $array['client_id'] = $app->isAdmin() ? '1' : '0';
        }
		
        // set $this->object_id
        // by first getting the scope_id
            // create a new scope if necessary
            $array['scope_id'] = $this->findScope( $array );
            
        // then getting the related object_id
            // create the object if necessary
            $this->object_id = $this->findObject($array);
            
        // TODO make this method return false with error reporting    
        return true;
	}
	
    /**
     * Given a named array with values, 
     * will intelligently set the log object's properties
     *  
     * @param array $array
     * @return boolean
     */
    function setSubject( $array )
    {
        // using these:
//                'value'=>'62',      // required. the subject's unique identifier, generally a user id #
//                'name'=>'Admin',    // required. the subject's name, generally a user's name or username.
//                'type'=>'user'      // optional. 'user' is the default
    	
        // set $this->subject_id
        // by first getting the subjecttype_id
            // create a new subjecttype if necessary
            $array['subjecttype_id'] = $this->findSubjectType( $array );
            
        // then getting the related subject_id
            // create the subject if necessary
            $this->subject_id = $this->findSubject($array);
            
        // TODO make this method return false with error reporting    
        return true;
    }
    
    /**
     * Given a named array with values, 
     * will intelligently set the log object's properties
     *  
     * @param array $array
     * @return boolean
     */
    function setVerb( $array )
    {
        // using these:
//                'value'=>'modified',    // required. unique identifier for this action
//                'name'=>'Modified'      // optional. if this is a new verb, this is the plain English name for it
        
        // set $this->verb_id            
            // create the verb if necessary
            $this->verb_id = $this->findVerb($array);
            
        // TODO make this method return false with error reporting    
        return true;
    }
	
	/**
	 * Loads a scope object
	 * creating a new one if necessary
	 * 
	 * @param unknown_type $array
	 * @return unknown_type
	 */
	function findScope( $array )
	{
        // get a scout scopes object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $scope = JTable::getInstance( 'Scopes', 'ScoutTable' );
        $scope->load( array('client_id'=>$array['client_id'], 'scope_identifier'=>$array['scope_identifier']) );
        if (empty($scope->scope_id))
        {
        	$scope->bind($array);
            if (!$scope->save())
            {
                JError::raiseNotice( 'ScoutTableLogs01', "ScoutTableLogs :: ". $scope->getError() );
            }
        }
        return $scope->scope_id;
	}
	
    /**
     * Loads an objects object
     * creating a new one if necessary
     * 
     * @param unknown_type $array
     * @return unknown_type
     */
    function findObject( $array )
    {
        // get a scout objects object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $object = JTable::getInstance( 'Objects', 'ScoutTable' );
        $object->load( array('scope_id'=>$array['scope_id'], 'object_value'=>$array['value']) );
        if (empty($object->object_id))
        {
            $object->object_name    = $array['name'];
            $object->scope_id       = $array['scope_id'];
            $object->object_value   = $array['value'];
            if (!$object->save())
            {
            	JError::raiseNotice( 'ScoutTableLogs01', "ScoutTableLogs :: ". $object->getError() );
            }
        }
        return $object->object_id;
    }
    
    /**
     * Loads a subjects object
     * creating a new one if necessary
     * 
     * @param unknown_type $array
     * @return unknown_type
     */
    function findSubject( $array )
    {
        // get a scout objects object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $subject = JTable::getInstance( 'Subjects', 'ScoutTable' );
        $subject->load( array('subjecttype_id'=>$array['subjecttype_id'], 'subject_value'=>$array['value']) );
        if (empty($subject->subject_id))
        {
            $subject->subject_name    = $array['name'];
            $subject->subjecttype_id  = $array['subjecttype_id'];
            $subject->subject_value   = $array['value'];
            if (!$subject->save())
            {
            	JError::raiseNotice( 'ScoutTableLogs01', "ScoutTableLogs :: ". $subject->getError() );
            }
        }
        return $subject->subject_id;
    }
    
    /**
     * Loads a subjectype object
     * creating a new one if necessary
     * 
     * @param unknown_type $array
     * @return unknown_type
     */
    function findSubjectType( $array )
    {
        // get a scout subjectype object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $subjecttype = JTable::getInstance( 'SubjectTypes', 'ScoutTable' );
        $subjecttype->load( array('subjecttype_value'=>$array['type']) );
        if (empty($subjecttype->subjecttype_id))
        {
            $subjecttype->subjecttype_name    = $array['type'];
            $subjecttype->subjecttype_value   = $array['type'];
            if (!$subjecttype->save())
            {
            	JError::raiseNotice( 'ScoutTableLogs01', "ScoutTableLogs :: ". $subjecttype->getError() );
            }
        }
        return $subjecttype->subjecttype_id;
    }
    
    /**
     * Loads a verb object
     * creating a new one if necessary
     * 
     * @param unknown_type $array
     * @return unknown_type
     */
    function findVerb( $array )
    {
        // get a scout verb object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
        $verb = JTable::getInstance( 'Verbs', 'ScoutTable' );
        $verb->load( array('verb_value'=>$array['value']) );
        if (empty($verb->verb_id))
        {
            $verb->verb_name    = $array['name'];
            $verb->verb_value   = $array['value'];
            if (!$verb->save())
            {
            	JError::raiseNotice( 'ScoutTableLogs01', "ScoutTableLogs :: ". $verb->getError() );
            }
        }
        return $verb->verb_id;
    }
	
}
