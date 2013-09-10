<?php
/**
 * @package Fingertips
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
 
defined('_JEXEC') or die('Restricted Access');

require_once(dirname(__FILE__).DS.'helper.php');

$document = &JFactory::getDocument();
$helper = new modScoutActivityHelper();

if ($helper->isInstalled())
{
    $limit = $params->get('limit', '10');
    $items = $helper->getSiteWideActivity($limit);
	require( JModuleHelper::getLayoutPath( 'mod_scout_activity' ) );	
}
    else
{
	require(JModuleHelper::getLayoutPath('mod_scout_activity','notinstalled'));
}


?>