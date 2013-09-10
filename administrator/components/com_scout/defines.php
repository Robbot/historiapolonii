<?php
/**
* @version		0.1.0
* @package		Scout
* @copyright	Copyright (C) 2009 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Scout extends JObject
{
    static $_version 		= '0.6.0';
    static $_versiontype    = 'community';
    static $_copyrightyear 	= '2010';
    static $_name 			= 'scout';
    static $_min_php		= '5.2';

    /**
     * Get the version
     */
    public static function getVersion()
    {
        return self::$_version;
    }

    /**
     * Get the copyright year
     */
    public static function getCopyrightYear()
    {
        return self::$_copyrightyear;
    }

    /**
     * Get the Name
     */
    public static function getName()
    {
        return self::$_name;
    }
    
    /**
     * Get the Minimum Version of Php
     */
    public static function getMinPhp()
    {
	    //get version from PHP. Note this should be in format 'x.x.x' but on some systems will look like this: eg. 'x.x.x-unbuntu5.2'
    	$phpV = self::getServerPhp();
    	$minV = self::$_min_php; 
    	$passes = false;
	
	    if ($phpV[0] >= $minV[0]) {
	        if (empty($minV[2]) || $minV[2] == '*') {
	            $passes = true;
	        } elseif ($phpV[2] >= $minV[2]) {
	            if (empty($minV[4]) || $minV[4] == '*' || $phpV[4] >= $minV[4]) {
	                $passes = true;
	            }
	        }
	    }
	    //if it doesn't pass raise a Joomla Notice
	    if (!$passes) :
	    	JError::raiseNotice('VERSION_ERROR',sprintf(JText::_('ERROR_PHP_VERSION'),$minV,$phpV));
	    endif;

	    //return minimum PHP version
	    return self::$_min_php;
    }
    
    public static function getServerPhp()
    {
    	return PHP_VERSION;
    }    
    

	/**
     * Get the URL to the folder containing all media assets
     *
     * @param string	$type	The type of URL to return, default 'media'
     * @return 	string	URL
     */
    public static function getURL($type = 'media')
    {
    	$url = '';

    	switch($type)
    	{
    		case 'media' :
    			$url = JURI::root(true).'/media/com_scout/';
    			break;
    		case 'css' :
    			$url = JURI::root(true).'/media/com_scout/css/';
    			break;
    		case 'images' :
    			$url = JURI::root(true).'/media/com_scout/images/';
    			break;
    		case 'js' :
    			$url = JURI::root(true).'/media/com_scout/js/';
    			break;
    	}

    	return $url;
    }

	/**
     * Get the path to the folder containing all media assets
     *
     * @param 	string	$type	The type of path to return, default 'media'
     * @return 	string	Path
     */
    public static function getPath($type = 'media')
    {
    	$path = '';

    	switch($type)
    	{
    		case 'media' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_scout';
    			break;
    		case 'css' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_scout'.DS.'css';
    			break;
    		case 'images' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_scout'.DS.'images';
    			break;
    		case 'js' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_scout'.DS.'js';
    			break;
    	}

    	return $path;
    }

	/**
	 * Method to dump the structure of a variable for debugging purposes
	 *
	 * @param	mixed	A variable
	 * @param	boolean	True to ensure all characters are htmlsafe
	 * @return	string
	 * @since	1.5
	 * @static
	 */
	public static function dump( &$var, $htmlSafe = true ) {
		$result = print_r( $var, true );
		return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	}
}

	// TODO Merge this class into base defines
class ScoutConfig extends Scout
{

	var $show_linkback						= '1';
	var $page_tooltip_dashboard_disabled	= '0';
	var $page_tooltip_config_disabled		= '0';
	var $page_tooltip_tools_disabled		= '0';

	/**
	 * constructor
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		$this->setVariables();
	}

	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$query = "SELECT * FROM #__scout_config";
		return $query;
	}

	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData() {
		// load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$database = &JFactory::getDBO();
			$query = $this->_buildQuery();
			$database->setQuery( $query );
			$this->_data = $database->loadObjectList();
		}

		return $this->_data;
	}

	/**
	 * Set Variables
	 *
	 * @acces	public
	 * @return	object
	 */
	function setVariables() {
		$success = false;

		if ( $data = $this->getData() )
		{
			for ($i=0; $i<count($data); $i++)
			{
				$title = $data[$i]->config_name;
				$value = $data[$i]->value;
				if (!empty($title)) {
					$this->$title = $value;
				}
			}

			$success = true;
		}

		return $success;
	}

	/**
	 * Get component config
	 *
	 * @acces	public
	 * @return	object
	 */
	function &getInstance() {
		static $instance;

		if (!is_object($instance)) {
			$instance = new ScoutConfig();
		}

		return $instance;
	}
}
?>