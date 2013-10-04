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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class XGalleryControllerCategories extends JController {
	
	function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('apply', 'save');
		$this->registerTask('accessregistered', 'setAccess');
		$this->registerTask('accessspecial', 'setAccess');		
		$this->registerTask('accesspublic', 'setAccess');
	}

	function edit() {
		JRequest::setVar('view', 'single');
		$this->display();
	}

	function add() {
		JRequest::setVar('view', 'single');
		$this->display();
	}

	function save() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
		$row =& JTable::getInstance('category', 'Table');
			
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError());
		}

		$row->quicktake = JRequest::getVar('quicktake', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$row->description = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
			
		if (!$row->creation_date) {
			$row->creation_date = date('Y-m-d H:i:s');
		}
		
		if($row->ordering == '') {
			$row->ordering = GalleryAdminHelper::getNextCategoryOrder($row->pid);
		}
		
		if (!$row->store()) {
			JError::raiseError(500, $row->getError());
		}
		
		$reqType = JRequest::getVar('rq', '');
		$message = '';
						
		if($reqType == 'image' && JRequest::getVar('thumb') == '') {
			$message = JTEXT::_('PLEASE SELECT THUMBNAIL');
		}
		
		$session =& JFactory::getSession();
		$id_cat_tmp = $session->get('id_cat_tmp', '', 'xgallery');		
		
		if(!empty($id_cat_tmp)) {
			$row =& JTable::getInstance('CategoryTmp', 'Table');
			$row->delete($id_cat_tmp);
			$session->set('id_cat_tmp', '', 'xgallery');
		}

		if($this->getTask() == 'apply') {
			$this->setRedirect('index.php?option='.$option.'&controller=categories&task=edit&id='.$row->id, 'Changes Applied'.' '.$message);
		} else {
			$this->setRedirect('index.php?option='.$option.'&controller=categories&view=categories', 'Category Saved');
		}
			
	}
	
	function setAccess() {
		
		global $option;
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('category', 'Table');
		$row->load( $id );
		
		switch( $this->getTask()) {
			case 'accessspecial':
				$access = '2';
				$groupname = 'Special';
				break;
			case 'accessregistered':
				$access = '1';
				$groupname = 'Registered';
				break;
			default:
				$access = '0';
				$groupname = 'Public';
		}

		$row->access = $access;
		$row->groupname = $groupname;

		if ( !$row->check() ) {
			return $row->getError();
		}
		if ( !$row->store() ) {
			return $row->getError();
		}

		$this->setRedirect( 'index.php?option=' . $option.'&controller=categories&view=categories');
	}

	function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
		$id = JRequest::getVar('cid', array());
		$row =& JTable::getInstance('category', 'Table');
			
		$publish = 1;
			
		if($this->getTask() == 'unpublish') {
			$publish = 0;
		}
			
		if(!$row->publish($id, $publish)) {
			JError::raiseError(500, $row->getError());
		}
			
		$s = '';
			
		if(count($id) >1) {
			$s = 's';
		}
			
		$msg = 'Category' . $s;
			
		if($this->getTask() == 'unpublish') {
			$msg .= ' unpublished';
		} else {
			$msg .= ' published';
		}
			
		$this->setRedirect('index.php?option='.$option.'&controller=categories', $msg);
	}

	function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
		$m = '';
			
		$ids = JRequest::getVar('cid', array(0));
		$row =& JTable::getInstance('category', 'Table');
			
		foreach($ids as $id) {
			$id = (int) $id;
			if($id != 1) {
				if(!$row->delete($id)) {
					JError::raiseError(500, $row->getError());
				}
			} else {
				$m = 'You cannot delete main category.';
			}
		}
			
		$s = '';
		$resp = '';
			
		if(count($ids) > 1) {
			$resp = 'Categories deleted.';
		} else {
			$resp = 'Category deleted.';
		}
		
		if($m != '' && count($ids) == 1) {
			$resp = $m;
		} elseif($m != '' && count($ids) > 1) {
			$resp .= ' '.$m;
		}
			
		$this->setRedirect('index.php?option='. $option.'&controller=categories&view=categories', $resp);
	}
	
	function orderup() 	{
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('category', 'Table');
		$row->load( $id );
		$row->move(-1, ' pid = '.(int) $row->pid);

		$link = 'index.php?option=com_xgallery&controller=categories&view=categories';
		$this->setRedirect( $link );
	}

	function orderdown() 	{
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('category', 'Table');
		$row->load( $id );
		$row->move(1, ' pid = '.(int) $row->pid);
		
		$link = 'index.php?option=com_xgallery&controller=categories&view=categories';
		$this->setRedirect( $link );
	}
		
	function saveorder() {
		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		
		$row =& JTable::getInstance('category', 'Table');
		$groupings = array();
		
		//$catid is null
		// update ordering values
		for( $i=0; $i < count($cid); $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->pid;

			if ($row->ordering != $order[$i]) {
				
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('pid = '.(int) $group);
		}
		
		$msg = JText::_( 'New ordering saved' );
		$link = 'index.php?option=com_xgallery&controller=categories&view=categories';
		$this->setRedirect( $link, $msg  );
	}
		
	/**
	 * Method to get a pagination object for the categories
	 **/
	function getPagination(){
		// Lets load the categories if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}
	
	function display() {
		$view = JRequest::getVar('view');
		if (!$view) {
			JRequest::setVar('view', 'categories');
		}
		parent::display();
	}
}