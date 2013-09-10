<?php
/**
 * @package   Scout
 * @author    Dioscouri Design
 * @link      http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

KLoader::load('site::plg.koowa.default');

/**
 * PlgKoowaScout
 *
 * Plugin for Scout that logs every add, edit and delete event in every Nooku Framework powered extension
 * 
 * @author Stian Didriksen <stian@nooku.org>
 */
class PlgKoowaScout extends PlgKoowaDefault
{
	/**
	 * Constructor
	 *
	 * Prevents plugin from being executed if Scout isn't installed
	 *
	 * @param  $dispatcher
	 * @param  $config
	 */
	public function __construct($dispatcher, $config = array())
	{
		if(!$this->isInstalled()) return false;

		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_scout/tables' );

		parent::__construct($dispatcher, $config);
	}

	/**
	 * controller.after.read event
	 *
	 * records read topics
	 *
	 * @author Stian Didriksen <stian@nooku.org>
	 * @param  $context
	 */
	public function onControllerAfterRead(KCommandcontext $context)
	{
		//Read actions likely return a string instead of the data
		//So we clone the context and override the result
		$clone			= clone $context;
		$item			= $clone->caller->getModel()->getItem();
		$clone->result	= $item;

		$this->createLogEntry($clone);
	}
	
	/**
	 * controller.after.add event
	 *
	 * records when something is added
	 *
	 * @author Stian Didriksen <stian@nooku.org>
	 * @param  $context
	 */
	public function onControllerAfterAdd(KCommandcontext $context)
	{
		$this->createLogEntry($context, 'created');
	}
	
	/**
	 * controller.after.edit event
	 *
	 * records when something is edit
	 *
	 * @author Stian Didriksen <stian@nooku.org>
	 * @param  $context
	 */
	public function onControllerAfterEdit(KCommandcontext $context)
	{
		$this->createLogEntry($context, 'modified');
	}
	
	/**
	 * controller.after.delete event
	 *
	 * records when something is deleted
	 *
	 * @author Stian Didriksen <stian@nooku.org>
	 * @param  $context
	 */
	public function onControllerAfterDelete(KCommandcontext $context)
	{
		$this->createLogEntry($context, 'deleted');
	}

	/**
	 * Determines if Scout is installed
	 * and registers necessary classes with the autoloader
	 * 
	 * @return boolean
	 */
	public function isInstalled()
	{
		jimport( 'joomla.filesystem.file' );
		return JFile::exists(JPATH_ADMINISTRATOR.'/components/com_scout/defines.php');
	}

	/**
	 * Create a Scout log entry of site activity
	 *
	 * Usage, using controller.after.read as example:<pre>
	 * public function onControllerAfterRead(KCommandcontext $context)
	 * {
	 *     $this->createLogEntry($context);
	 * }
	 * </pre>
	 * 
	 * @param  KCommandcontext $context
	 * 
	 * @return boolean
	 */
	public function createLogEntry(KCommandcontext $context, $action = false)
	{
		if ( !$this->isInstalled() || JFactory::getUser()->guest ) return;

		//The caller is a reference to the object that is triggering this event
		$caller = $context['caller'];

		//the result will contain the result of the controller action.
		$result = $context['result'];

		//The identifier
		$identifier = $caller->getIdentifier();
		
		if(!is_a($result, 'KDatabaseRowsetAbstract') && !is_array($result)) $result = array($result);
		
		foreach($result as $row)
		{
			// get the object if possible
			if (!$object = $this->getObject( $context, $row )) return;
	
			// get a scout logs object
			$log = JTable::getInstance( 'Logs', 'ScoutTable' );
	  
			// set the subject
			$log->setSubject(array( 'value' => JFactory::getUser()->id, 'name' => JFactory::getUser()->name, 'type' => 'user' ));
	
			// set the verb and object
			if(!$action) $action = $caller->getAction();
			$log->setVerb(array( 'value' => $action, 'name' => JText::_(KInflector::humanize($action)) ));
			$log->setObject($object);
	  
			$log->save();
		}
	}


	/**
	 * Sets the object array 
	 * Sets it with some default values if not being set manually
	 * to make its creation as easy as possible
	 * 
	 * @return array
	 */
	public function getObject( KCommandcontext $context, $result = false, $object = array() )
	{

		//The caller is a reference to the object that is triggering this event
		$caller = $context['caller'];

		// The event result is usually a data row
		if(!$result) $result = $context['result'];

		//If the result isn't an object, don't try and treat it like one
		if(!is_object($result)) return false;

		// Get the caller identifier
		$identifier = $caller->getIdentifier();

		$object = new KConfig($object);
	
		$object->append(array(
			'value'				=> $result->id,
			'name'				=>  isset($result->title) ? $result->title : 'Untitled',
			'scope_identifier'	=> 'option=com_' . $identifier->package . '&view=' . $identifier->name,
			'scope_name'		=> JText::_(KInflector::humanize($identifier->package)) . ' / ' . KInflector::humanize($identifier->name),
			'scope_url'			=> 'index.php?option=com_' . $identifier->package . '&view=' . $identifier->name . '&id=',
			'client_id'			=> $identifier->application == 'admin'
		));

		if (empty($object->value) || empty($object->name) || empty($object->scope_identifier)) return false;

  		return $object->toArray();
	}
}