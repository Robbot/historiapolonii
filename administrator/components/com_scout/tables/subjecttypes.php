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

class ScoutTableSubjectTypes extends ScoutTable 
{
	function ScoutTableSubjectTypes ( &$db ) 
	{
		
		$tbl_key 	= 'subjecttype_id';
		$tbl_suffix = 'subjecttypes';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'scout';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if (empty($this->subjecttype_name))
		{
			$this->setError( JText::_( "SubjectType Name Required" ) );
			return false;
		}
	    if (empty($this->subjecttype_value))
        {
            $this->setError( JText::_( "SubjectType Value Required" ) );
            return false;
        }
		return true;
	}
}
