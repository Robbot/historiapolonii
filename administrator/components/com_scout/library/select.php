<?php
/**
* @version		0.1.0
* @package		Scout
* @copyright	Copyright (C) 2009 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'select.php' );

class ScoutSelect extends JHTMLSelect
{
	/**
	* Generates a yes/no radio list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function booleans( $selected, $name = 'filter_enabled', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select State', $yes = 'Enabled', $no = 'Disabled' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  '0', JText::_( $no ) );
		$list[] = JHTML::_('select.option',  '1', JText::_( $yes ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

	/**
	* Generates range list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function range( $selected, $name = 'filter_range', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Range' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  'today', JText::_( "Today" ) );
		$list[] = JHTML::_('select.option',  'yesterday', JText::_( "Yesterday" ) );
		$list[] = JHTML::_('select.option',  'last_seven', JText::_( "Last Seven Days" ) );
		$list[] = JHTML::_('select.option',  'last_thirty', JText::_( "Last Thirty Days" ) );
		$list[] = JHTML::_('select.option',  'ytd', JText::_( "Year to Date" ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

    /**
    * Generates a created/modified select list
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function datetype( $selected, $name = 'filter_datetype', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select Type' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  'created', JText::_( "Created" ) );
        $list[] = JHTML::_('select.option',  'modified', JText::_( "Modified" ) );
        
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }
    
    /**
     * Displays a selectlist of all the scopes in the DB
     * 
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @return unknown_type
     */
    public static function scope($selected, $name = 'filter_scope', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false)
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( 'Select Scope' )." -", 'scope_id', 'scope_name' );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'models' );
        $model = JModel::getInstance( 'Scopes', 'ScoutModel' );
        $model->setState( 'order', 'scope_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->scope_id, JText::_($item->scope_name), 'scope_id', 'scope_name' );
        }

        return self::genericlist($list, $name, $attribs, 'scope_id', 'scope_name', $selected, $idtag );
    }
    
    /**
     * Displays a selectlist of all the subjects in the DB
     * 
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @return unknown_type
     */
    public static function subject($selected, $name = 'filter_subject', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false)
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( 'Select Subject' )." -", 'subject_id', 'subject_name' );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'models' );
        $model = JModel::getInstance( 'Subjects', 'ScoutModel' );
        $model->setState( 'order', 'subject_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->subject_id, JText::_($item->subject_name), 'subject_id', 'subject_name' );
        }

        return self::genericlist($list, $name, $attribs, 'subject_id', 'subject_name', $selected, $idtag );
    }
    
    /**
     * Displays a selectlist of all the verbs in the DB
     * 
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @return unknown_type
     */
    public static function verb($selected, $name = 'filter_verb', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false)
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( 'Select Verb' )." -", 'verb_id', 'verb_name' );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'models' );
        $model = JModel::getInstance( 'Verbs', 'ScoutModel' );
        $model->setState( 'order', 'verb_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->verb_id, JText::_($item->verb_name), 'verb_id', 'verb_name' );
        }

        return self::genericlist($list, $name, $attribs, 'verb_id', 'verb_name', $selected, $idtag );
    }
    
    /**
     * Displays a selectlist of all the objects in the DB
     * 
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @return unknown_type
     */
    public static function object($selected, $name = 'filter_object', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false)
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( 'Select Object' )." -", 'object_id', 'object_name' );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'models' );
        $model = JModel::getInstance( 'Objects', 'ScoutModel' );
        $model->setState( 'order', 'object_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->object_id, JText::_($item->object_name), 'object_id', 'object_name' );
        }

        return self::genericlist($list, $name, $attribs, 'object_id', 'object_name', $selected, $idtag );
    }
}
