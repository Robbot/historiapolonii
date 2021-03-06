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
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_scout.views._base', JPATH_ADMINISTRATOR.DS.'components' );

class ScoutViewConfig extends ScoutViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
	}
	
	/**
	 * 
	 * @return void
	 **/
	function _default($tpl = null) 
	{
		JLoader::import( 'com_scout.library.select', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_scout.library.grid', JPATH_ADMINISTRATOR.DS.'components' );
		JLoader::import( 'com_scout.library.tools', JPATH_ADMINISTRATOR.DS.'components' );

		// check config
			$row = ScoutConfig::getInstance();
			$this->assign( 'row', $row );
		
		// add toolbar buttons
			JToolBarHelper::save('save');
			JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
			
		// plugins
        	$filtered = array();
	        $items = ScoutHelperTools::getPlugins();
			for ($i=0; $i<count($items); $i++) 
			{
				$item = &$items[$i];
				// Check if they have an event
				if ($hasEvent = ScoutHelperTools::hasEvent( $item, 'onListConfigScout' )) {
					// add item to filtered array
					$filtered[] = $item;
				}
			}
			$items = $filtered;
			$this->assign( 'items_sliders', $items );
			
		// Add pane
			jimport('joomla.html.pane');
			$sliders = JPane::getInstance( 'sliders' );
			$this->assign('sliders', $sliders);
			
		// form
			$validate = JUtility::getToken();
			$form = array();
			$view = strtolower( JRequest::getVar('view') );
			$form['action'] = "index.php?option=com_scout&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
			
		// set the required image
		// TODO Fix this to use defines
			$required = new stdClass();
			$required->text = JText::_( 'Required' );
			$required->image = "<img src='".JURI::root()."/media/com_scout/images/required_16.png' alt='{$required->text}'>";
			$this->assign('required', $required );
    }
    
}
