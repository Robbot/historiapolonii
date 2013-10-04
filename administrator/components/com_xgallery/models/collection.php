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

class XGalleryModelCollection extends JModel {
	
	private $_row = null;
	private $_maxHeight = null;
	private $_maxWidth = null;
	private $_imageQuality = null;
	private $_resizeImage = null;
	private $_resizeImageFunction = null;
	private $_file_size_limit = null;
  	private $_file_upload_limit = null;
  	private	$_file_queue_limit = null;
  	private $_swupload_debug = null;
	
	function getData() {
		$session =& JFactory::getSession();
		$id_coll_tmp = $session->get('id_coll_tmp', '', 'xgallery');		
		
		if(empty($id_coll_tmp)) {
			$this->_row =& JTable::getInstance('Collection', 'Table');
			$id = JRequest::getVar('id');
			$this->_row->load($id);		
		} else {
			$this->_row =& JTable::getInstance('CollectionTmp', 'Table');
			$this->_row->load($id_coll_tmp);
		}
		
		if(!isset($this->_row->ordering)) {
			$this->_row->ordering = '';
		}
		
		return $this->_row;
	}	
	
	function getUserNames() {
		$query = 'SELECT id, username'
			. ' FROM #__users '			
		;
		
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		
		return $rows;			
	}
	
	function getSWFUploadScript() {
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		$session = & JFactory::getSession();
		$id = JRequest::getVar('id', '');
		$this->_file_size_limit = $cfgParams->get('file_size_limit', 2048);
  		$this->_file_upload_limit = (int)$cfgParams->get('file_upload_limit', 0);
  		$this->_file_queue_limit = (int)$cfgParams->get('file_queue_limit', 100);
  		$this->_swupload_debug = (bool)$cfgParams->get('swfupload_debug', 0);
  		
		if($this->_resizeImage) {
  			$this->_resizeImageFunction = 'cUploadComplete';
  		} else {
  			$this->_resizeImageFunction = 'uploadComplete';
  		}
  		
		if($this->_swupload_debug) {
  			$debug = "true";
  		} else {
  			$debug = "false";
  		}

		$swfUploadHeadJs ='
		var swfu;
		
		jQuery(document).ready(function() {
			var settings = {
				flash_url : "'.JURI::base(true).'/components/com_xgallery/assets/swfupload/swfupload.swf",				
				upload_url: "'.JURI::base().'index.php",
				post_params: {		                		
		                "option" : "com_xgallery",
		                "controller" : "upload",
		                "format" : "raw",
		                "id" : "'.$id.'",
		                "'.$session->getName().'" : "'.$session->getId().'"},
		        use_query_string : true, 
				file_size_limit : "'. $this->_file_size_limit .'",
				file_types : "*.jpg;*.jpeg;*.gif;*.png",
				file_types_description : "All Files",
				file_upload_limit : '. $this->_file_upload_limit .',
				file_queue_limit : '. $this->_file_queue_limit .',
				file_post_name : "Filedata",
				custom_settings : {
					upload_target : "divFileProgressContainer",
					cancelButtonId : "btnCancel"
				},
				debug: '.$debug.',

				// Button settings
				button_image_url: "'.JURI::root().'administrator/components/com_xgallery/assets/swfupload/images/SmallSpyGlassWithTransperancy_17x18.png",			
				button_width: "180",
				button_height: "18",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: "'."<span class='button'>".JText::_('SELECT IMAGES')."</span>".'",
				button_text_style: ".button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }",
				button_text_left_padding: 18,
				button_text_top_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// The event handler functions are defined in handlers.js
				//swfupload_preload_handler : preLoad,
				//swfupload_load_failed_handler : loadFailed,
				swfupload_loaded_handler : swfUploadLoaded,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : cUploadSuccess,
				upload_complete_handler : ' . $this->_resizeImageFunction . '				
			};

			swfu = new SWFUpload(settings);

	     });
		 
		';
		return $swfUploadHeadJs;
	}
	
	function getImageList() {
		jimport('joomla.filesystem.file');
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$cookieParams = GalleryHelper::getCookieParams();
  		
		if($cfgParams->get('image_external')) {
			$bpath = $cookieParams->bpath . DS . $this->_row->folder;
		} else {
			$bpath = COM_MEDIA_BASE . DS .  $this->_row->folder;
		}

		if(JFolder::exists($bpath)) {
			$files = JFolder::files($bpath, '', false, false);
		} else {
			$files = array();
		}
		
		sort($files);
		
		return $files;
	}
}