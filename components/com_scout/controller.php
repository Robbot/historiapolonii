<?php
/**
 * @version	0.1
 * @package	Scout
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class ScoutController extends JController 
{	
	/**
	 * 
	 * @return 
	 */
	function doTask()
	{
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
		$msg->error = '';
				
		// expects $element in URL and $elementTask
		$element = JRequest::getVar( 'element', '', 'request', 'string' );
		$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );

		$msg->error = '1';
		// $msg->message = "element: $element, elementTask: $elementTask";
		
		// gets the plugin named $element
		$import 	= JPluginHelper::importPlugin( 'scout', $element );
		$dispatcher	=& JDispatcher::getInstance();
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
		$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element) 
		$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];
						
		// encode and echo (need to echo to send back to browser)		
		echo $msg->message;
		$success = $msg->message;

		return $success;
	}
	
	/**
	 * 
	 * @return 
	 */
	function doTaskAjax()
	{
		$success = true;
		$msg = new stdClass();
		$msg->message = '';
				
		// get elements $element and $elementTask in URL 
			$element = JRequest::getVar( 'element', '', 'request', 'string' );
			$elementTask = JRequest::getVar( 'elementTask', '', 'request', 'string' );
			
		// get elements from post
			// $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
			
		// for debugging
			// $msg->message = "element: $element, elementTask: $elementTask";

		// gets the plugin named $element
			$import 	= JPluginHelper::importPlugin( 'scout', $element );
			$dispatcher	=& JDispatcher::getInstance();
			
		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
			$result 	= $dispatcher->trigger( $elementTask, array( $element ) );
		// This should be a concatenated string of all the results, 
			// in case there are many plugins with this eventname 
			// that return null b/c their filename != element)
			$msg->message = implode( '', $result );
			// $msg->message = @$result['0'];

		// set response array
			$response = array();
			$response['msg'] = $msg->message;
			
		// encode and echo (need to echo to send back to browser)
			echo ( json_encode( $response ) );

		return $success;
	}
	
	
}

?>