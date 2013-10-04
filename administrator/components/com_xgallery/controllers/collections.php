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

class XGalleryControllerCollections extends JController {
	
	function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('apply', 'save');
		$this->registerTask('accessregistered', 'setAccess');
		$this->registerTask('accessspecial', 'setAccess');		
		$this->registerTask('accesspublic', 'setAccess');
	}

	function edit() {
		JRequest::setVar('view', 'collection');
		$this->display();
	}

	function add() {
		JRequest::setVar('view', 'collection');
		$this->display();
	}

	function save() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
		$row =& JTable::getInstance('collection', 'Table');
			
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError());
		}
			
		$row->quicktake = JRequest::getVar('quicktake', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$row->description = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		$max = ini_get('upload_max_filesize');
		//$file_type = $params->get( 'type' );
		$file_type = array("image/png", "image/gif", "image/jpeg", "application/zip", "application/x-zip-compressed", "application/x-gzip");
        
		$jcfg = new JConfig();  		
		$temp_path = $jcfg->tmp_path;
		
		$helper = new GalleryHelper();
		$cookieParams = $helper->getCookieParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		$folder_exists = false;
		
		if($cfgParams->get('image_external')) {
			$base_folder_path = $cookieParams->bpath;
		} else {
			$base_folder_path = COM_MEDIA_BASE;
		}

		if (JRequest::getVar('folder') == '') {
			$user =& JFactory::getUser();
			$folder_path = $base_folder_path . DS . $user->username . DS . $user->username . time();
			$folder_exists = false;
		} else {
			$folder_path = $base_folder_path . DS . JRequest::getVar('folder');
			$folder_exists = true;
		}
		
		if(!JFolder::create($folder_path, 0755)) {
			JError::raiseWarning(100, JTEXT::_('COULD_NOT_CREATE_DIRECTORY'));
		}

		$info_result = MediaHelper::uploadFile($max, $folder_path, $temp_path, $file_type, $folder_exists);

		if($info_result['new_dir'] != '' && $row->folder == '') {
			$row->folder = MediaHelper::removeWhiteSpace(strtolower($info_result['new_dir']));
		}

		if (!$row->creation_date) {
			$row->creation_date = date('Y-m-d H:i:s');
		}
		
		$row->ordering = GalleryAdminHelper::getNextCollectionOrder($row->cid);

		if (!$row->store()) {
			JError::raiseError(500, $row->getError());
		}
		
		$session =& JFactory::getSession();
		$id_coll_tmp = $session->get('id_coll_tmp', '', 'xgallery');		
		
		if(!empty($id_coll_tmp)) {
			$row =& JTable::getInstance('CollectionTmp', 'Table');
			$row->delete($id_coll_tmp);	
			$session->set('id_coll_tmp', '', 'xgallery');
		}
			
		if($this->getTask() == 'apply') {

			$reqType = JRequest::getVar('rq', '');
			$message = '';
			
			if($reqType == 'folder') {
				//$message = JTEXT::_('PLEASE SELECT FOLDER');
			}

			if($info_result['new_dir'] != '') {
				$reqType = 'image';
			}
			
			if($reqType == 'image' && JRequest::getVar('thumb') == '') {
				//$message = JTEXT::_('PLEASE SELECT THUMBNAIL');
			} elseif ($reqType == 'folder' && JRequest::getVar('folder') != '') {
				//$message = JTEXT::_('PLEASE SELECT THUMBNAIL');
			}
			$this->setRedirect('index.php?option='.$option.'&task=edit&controller=collections&id='.$row->id, JTEXT::_('CHANGES APPLIED').' '.$message);
		} else {
			$this->setRedirect('index.php?option='.$option.'&controller=collections&view=collections', JTEXT::_('COLLECTION SAVED'));
		}
			
	}
	
	function setAccess() {
		
		global $option;
		//$id = (int)JRequest::getVar('id');
		//echo 'id: '.$id.'<br/>';
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('collection', 'Table');
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

		$this->setRedirect( 'index.php?option=' . $option.'&controller=collections&view=collections');
	}

	function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
		$id = JRequest::getVar('cid', array());
		$row =& JTable::getInstance('collection', 'Table');
			
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
			
		$msg = 'Collection' . $s;
			
		if($this->getTask() == 'unpublish') {
			$msg .= ' unpublished';
		} else {
			$msg .= ' published';
		}
			
		$this->setRedirect('index.php?option='.$option.'&controller=collections', $msg);
	}

	function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		global $option;
			
		$ids = JRequest::getVar('cid', array(0));
		$row =& JTable::getInstance('collection', 'Table');
			
		foreach($ids as $id) {
			$id = (int) $id;

			if(!$row->delete($id)) {
				JError::raiseError(500, $row->getError());
			}
		}
			
		$s = '';
			
		if(count($ids) > 1) {
			$s = 's';
		}
			
		$this->setRedirect('index.php?option='. $option.'&controller=collections&view=collections', 'Collection' . $s . ' deleted');
	}
	
	function orderup() 	{
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('collection', 'Table');
		$row->load( $id );
		$row->move(-1, ' cid = '.(int) $row->cid);

		$link = 'index.php?option=com_xgallery&controller=collections&view=collections';
		$this->setRedirect( $link );
	}

	function orderdown() 	{
		$cid = JRequest::getVar( 'cid', array());
		$id = (int)$cid[0];
		
		$row =& JTable::getInstance('collection', 'Table');
		$row->load( $id );
		$row->move(1, ' cid = '.(int) $row->cid);
		
		$link = 'index.php?option=com_xgallery&controller=collections&view=collections';
		$this->setRedirect( $link );
	}
		
	function saveorder() {
		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		
		$row =& JTable::getInstance('collection', 'Table');
		$groupings = array();
		
		//$catid is null
		// update ordering values
		$count = count($cid);
		
		for( $i=0; $i < $count; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->cid;

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
			$row->reorder('cid = '.(int) $group);
		}
		
		$msg = JText::_( 'New ordering saved' );
		$link = 'index.php?option=com_xgallery&controller=collections&view=collections';
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
			JRequest::setVar('view', 'collections');
		}
		parent::display();
	}
}