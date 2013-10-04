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
jimport('joomla.application.component.controller');

// Submenu view
$view	= JRequest::getVar( 'view', '', '', 'string', JREQUEST_ALLOWRAW );

if ($view == '' || $view == 'main') {
	JSubMenuHelper::addEntry(JText::_('Main'), 'index.php?option=com_xgallery', true);
	JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_xgallery&view=categories');
	JSubMenuHelper::addEntry(JText::_('Collections'), 'index.php?option=com_xgallery&view=collections' );
	JSubMenuHelper::addEntry(JText::_('Hits'), 'index.php?option=com_xgallery&view=hits' );
}

if ($view == 'categories') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_xgallery');
	JSubMenuHelper::addEntry(JText::_('Maps'), 'index.php?option=com_xgallery&view=categories', true);
	JSubMenuHelper::addEntry(JText::_('Markers'), 'index.php?option=com_xgallery&view=collections' );
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_xgallery&view=hits' );
}

if ($view == 'category') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_xgallery');
	JSubMenuHelper::addEntry(JText::_('Maps'), 'index.php?option=com_xgallery&view=categories');
	JSubMenuHelper::addEntry(JText::_('Markers'), 'index.php?option=com_xgallery&view=collections', true );
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_xgallery&view=hits' );
}


if ($view == 'collections') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_xgallery');
	JSubMenuHelper::addEntry(JText::_('Maps'), 'index.php?option=com_xgallery&view=categories');
	JSubMenuHelper::addEntry(JText::_('Markers'), 'index.php?option=com_xgallery&view=collections' );
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_xgallery&view=hits', true );
}

if ($view == 'collection') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_xgallery');
	JSubMenuHelper::addEntry(JText::_('Maps'), 'index.php?option=com_xgallery&view=categories');
	JSubMenuHelper::addEntry(JText::_('Markers'), 'index.php?option=com_xgallery&view=collections' );
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_xgallery&view=hits', true );
}

if ($view == 'hits') {
	JSubMenuHelper::addEntry(JText::_('Control Panel'), 'index.php?option=com_xgallery');
	JSubMenuHelper::addEntry(JText::_('Maps'), 'index.php?option=com_xgallery&view=categories');
	JSubMenuHelper::addEntry(JText::_('Markers'), 'index.php?option=com_xgallery&view=collections' );
	JSubMenuHelper::addEntry(JText::_('Info'), 'index.php?option=com_xgallery&view=hits', true );
}


class phocaMapsCpController extends JController
{
	function display() {
		parent::display();
	}
}
?>