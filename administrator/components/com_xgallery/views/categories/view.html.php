<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XGallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class XGalleryViewCategories extends JView {	
	
	function display($tpl = null) {
		global $mainframe, $option;
		$component = JComponentHelper::getComponent( 'com_xgallery' );
		$params = new JParameter( $component->params );
		$rows =& $this->get('data');
		$pagination =& $this->get('pagination');
		$search = $this->get('search');
		$db = & JFactory::getDBO();
		$cookieParams = GalleryHelper::getCookieParams();
				
		if($params->get('image_external')) {
			$bpath = $cookieParams->bpath;
		} else {
			$bpath = COM_MEDIA_BASEPATH;
		}
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_order', 	'filter_order', 	'ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_state', 	'filter_state', 	'', 'cmd' );
		$filter_type 		= $mainframe->getUserStateFromRequest( $option.'.categories.filter_type', 		'filter_type', 		'', 'cmd' );
		$filter_toplevel	= JArrayHelper::getValue( $_REQUEST, 'filter_toplevel', '' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.categories.search', 			'search', 			'', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		
		// build the html for published		
		$states[] = JHTML::_('select.option',  '', ' - '.JText::_( 'SELECT STATE' ).' - ' );
		$states[] = JHTML::_('select.option',  '1', JText::_( 'PUBLISHED' ) );
		$states[] = JHTML::_('select.option',  '0', JText::_( 'UNPUBLISHED' ) );
		$lists['state'] = JHTML::_('select.genericlist', $states, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		
						
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$lists['ordering'] = ($lists['order'] == 'ordering');//Ordering allowed ?
		
		$this->assignRef('lists', $lists);	
		$this->assignRef('rows', $rows);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('access', $access);
		$this->assignRef('categories', $this->get('category'));
		$this->assign('search', $search);
		$this->assign('rWidth', $params->get('rWidth_admin', 75));
		$this->assign('rHeight', $params->get('rHeight_admin', 75));
		$this->assign('bpath', $bpath);

		$pageNav 	= & $this->get( 'Pagination' );
		$this->assignRef('pageNav', $pageNav);
		
		$doc =& JFactory::getDocument();
		$doc->addStyleSheet(JURI::base(true).'/components/com_xgallery'.DS.'assets'.DS.'style.css');
		
		parent::display($tpl);
	}
}