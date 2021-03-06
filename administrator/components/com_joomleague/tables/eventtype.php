<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Event Table class
 *
 * @package	Joomleague
 * @since	0.1
 */
class TableEventtype extends JLTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $name;
	/* alias for nice sef urls */
	var $alias;
	var $icon;

	var $parent;
	var $splitt;
	var $direction;
	var $double;
	var $sports_type_id;

	var $published=1;
	var $ordering;

	var $checked_out;
	var $checked_out_time;

	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db)
	{
		parent::__construct('#__joomleague_eventtype', 'id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
		// setting alias
		if ( empty( $this->alias ) )
		{
			$this->alias = JFilterOutput::stringURLSafe( $this->name );
		}
		else {
			$this->alias = JFilterOutput::stringURLSafe( $this->alias ); // make sure the user didn't modify it to something illegal...
		}
		return true;
	}
}
?>