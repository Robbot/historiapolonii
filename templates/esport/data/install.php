<?php

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'administrator');

require_once dirname(dirname(__FILE__)) . DS . 'library' . DS . 'Artx.php';

require_once JPATH_BASE . DS . 'includes' . DS . 'defines.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'framework.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'helper.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'toolbar.php';

$mainframe = & JFactory::getApplication('administrator');
$mainframe->initialise(array('language' => $mainframe->getUserState('application.lang', 'lang')));

// checking user privileges
JPluginHelper::importPlugin('system');
$mainframe->triggerEvent('onAfterInitialise');
$user = $mainframe->getUser();
if ('super administrator' != strtolower($user->usertype) && 'administrator' != strtolower($user->usertype))
    exit('error:2:Content installation requires administrator privileges.');


ArtxLoadClass('Artx_Data_Loader');

$loader = new Artx_Data_Loader();
$loader->load('data.xml');
echo $loader->execute($_GET);
