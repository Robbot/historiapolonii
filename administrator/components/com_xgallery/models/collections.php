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

jimport('joomla.application.component.model');

class XGalleryModelCollections extends JModel {

	var $_data = null;
	var $_pagination = null;
	var $_total = null;
	var $_search = null;
	var $_query = null;
	
	function &getData() {
		
		$pagination =& $this->getPagination();
		
		if (empty($this->_data)) {
			$query = $this->buildSearch();
			$this->_data = $this->_getList($query, $pagination->limitstart, $pagination->limit);
		}
		
		return $this->_data;
	}
	
	function buildSearch() {
		if (!$this->_query) {
			$search = $this->getSearch();

			$this->_query = 	"SELECT a.*, c.countid AS countid";
			$this->_query .=	" FROM #__xgallery AS a";
			$this->_query .=	" JOIN (SELECT c.cid, count(*) AS countid";
			$this->_query .=	" FROM #__xgallery AS c";
			$this->_query .=	" GROUP BY c.cid) AS c";
			$this->_query .=	" ON a.cid = c.cid";

			$this->_query .= $this->_buildContentWhere();
			$this->_query .= $this->_buildContentOrderBy();
		}

		return $this->_query;
	}
	
	/**
	 * Method to build the where clause of the query
	 **/
	function _buildContentWhere() {
		global $mainframe, $option;

		$params 			= & JComponentHelper::getParams('com_xgallery');
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.collections.filter_state', 'filter_state', '', 'cmd' );
		$filter_type 		= $mainframe->getUserStateFromRequest( $option.'.collections.filter_type', 'filter_type', '', 'cmd' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.collections.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
		$filter_cat 		= $mainframe->getUserStateFromRequest( $option.'.collections.filter_cat', 'filter_cat', '', 'cmd' );
	
		$where = array();
		
		if ( $filter_cat != '' ) {
			$where[] = 'a.cid = '.$filter_cat;
		}

		if ( $filter_state != '') {
			$where[] = 'a.published = '.$filter_state;
		}
		
		if ($search) {
			if (!$filter_type) {
				$where[] = ' LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(quicktake) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(description) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 1) {
				$where[] = ' LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 2) {
				$where[] = ' LOWER(quicktake) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false ).
					' OR LOWER(description) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
			if ($filter_type == 3) {
				$where[] = ' LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
			}
		}
		
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function _buildContentOrderBy() {
        global $mainframe, $option;
 
        $filter_order     = $mainframe->getUserStateFromRequest( $option.'.collections.filter_order', 'filter_order', 'ordering', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'.collections.filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		
		if($filter_order == '' && $filter_order_Dir == '') {
        	$orderby = ' ORDER BY ' . $filter_order.' '.$filter_order_Dir;
        } else if($filter_order == 'ordering') {
        	$orderby = ' ORDER BY ordering '.$filter_order_Dir;
        } else {
        	$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
        }

        return $orderby;
	}
	
	
	function getCategory() {
		$query = "SELECT * FROM #__xgallery_categories";
		$cdata = $this->_getList($query);
		$list = array();
		
		if(empty($cdata)) {
			JError::raiseError(404, JTEXT::_('CATEGORY NOT FOUND'));
		}
		
		foreach($cdata as $data) {		
			$list[$data->id] = $data->name;
		}
		return $list;
	}
	
	function getTotal() {
		if (!$this->_total) {
			$query = $this->buildSearch();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	function &getPagination() {
		if(!$this->_pagination) {
			jimport('joomla.html.pagination');
			global $mainframe;
			$this->_pagination = new JPagination($this->getTotal(), JRequest::getVar('limitstart', 0), JRequest::getVar('limit', $mainframe->getCfg('list_limit')));
		}

		return $this->_pagination;
	}
	
	function getSearch()
	{
		if (!$this->_search) {
			global $mainframe, $option;

			$search = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
			$this->_search = JString::strtolower($search);
		}

		return $this->_search;
	}
	
	/**
	 * Method to get all Categories
	 **/
	function getCollCategories() {
		global $mainframe, $option;
		
		$category = array();

		$filter_cat = $mainframe->getUserStateFromRequest( $option.'.collections.filter_cat', 'filter_cat', '', 'cmd' );

		$query = 'SELECT id, name' .
				' FROM #__xgallery_categories' .
				' ORDER BY name';
		$this->_db->setQuery($query);

		$category[] 	= JHTML::_('select.option', '', ' - '.JText::_('SELECT CATEGORY').' - ', 'id', 'name');
		$categories 	= array_merge($category, $this->_db->loadObjectList());
		$return			= JHTML::_('select.genericlist',  $categories, 'filter_cat', 'class="inputbox" size="1" onchange="submitform( );"', 'id', 'name', $filter_cat);	
		return $return;
	}
	
	function getGalleryLocation() {
		jimport('joomla.filesystem.file');
		$helper = new GalleryHelper();
		$cookieParams = $helper->getCookieParams();
		
		$config = JFactory::getConfig();
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		
		if($cfgParams->get('image_external')) {
			$bpath = $cookieParams->bpath;
		} else {
			$bpath = COM_MEDIA_BASE;
		}		

		if(!JFolder::exists($bpath)) {
			$result = Array('ok' => false, 'mesg' => JText::_('GALLERY PATH NOT EXIST'));
			return $result;
		}
		
		if(is_writable($bpath)) {
			$result = Array('ok' => true, 'mesg' => JText::_('GALLERY PATH WRITABLE'));
			return $result;
		} else {
			$result = Array('ok' => false, 'mesg' => JText::_('GALLERY PATH NOT WRITABLE'));
			return $result;
		}
	}
}