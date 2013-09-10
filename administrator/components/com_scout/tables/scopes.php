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

class ScoutTableScopes extends ScoutTable 
{
	function ScoutTableScopes( &$db ) 
	{
		
		$tbl_key 	= 'scope_id';
		$tbl_suffix = 'scopes';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'scout';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
	    if (empty($this->client_id))
        {
            $this->client_id = 0;
        }
		$array = array('0', '1');
	    if (!in_array( (int) $this->client_id, $array))
        {
            $this->setError( JText::_( "Invalid Client ID" ) );
            return false;
        }
        
		if (empty($this->scope_name))
		{
			$this->setError( JText::_( "Scope Name Required" ) );
			return false;
		}
		
	    if (empty($this->scope_identifier))
        {
            $this->setError( JText::_( "Scope Identifier Required" ) );
            return false;
        }
		return true;
	}
}
