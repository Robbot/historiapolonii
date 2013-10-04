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
	
jimport('joomla.application.component.view');
require_once(JApplicationHelper::getPath('helper'));
	
class XGalleryViewCollection extends JView {
	function display($tpl = null) {
		global $mainframe;		
		//Load pane behavior
		jimport('joomla.html.pane');
		jimport('joomla.filesystem.file');

		$user =& JFactory::getUser();
		$session =& JFactory::getSession();
		$id_coll_tmp = $session->get('id_coll_tmp', '', 'xgallery');		
		$temp_show_swfupload = true;
		
		$row = $this->get('Data');
		
		$this->assign('row', $row);
		$editor =& JFactory::getEditor();		
		$this->assignRef('editor', $editor);
		$cids = fetchSelectList('', $row->cid, 'cid');
		$this->assignRef('cid', $cids);
		$cookieParams = GalleryHelper::getCookieParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$pane = & JPane::getInstance('sliders');
  		$this->assignRef('pane', $pane);
  		
  		$doc =& JFactory::getDocument();
  		
  		$doc->addScript(JURI::root(true).'/components/com_xgallery/js/jquery.js');
  		
  		$jQueryJS = '  			
  			if($===jQuery){jQuery.noConflict();};
  			';
  		$doc->addScriptDeclaration($jQueryJS);
  		
		$doc->addStyleSheet(JURI::base(true).'/components/com_xgallery/assets/style.css');
		
  		$jcfg = new JConfig();
  		
		$temp_path = $jcfg->tmp_path;

		$newFolder = '';
		$newFolder = JRequest::getVar('folder');
		
		if($newFolder != '') {
			$row->folder = $newFolder;
		}
		
		$rq = JRequest::getVar('reqType', '');
		if ($rq == 'image') {
			$this->assign('rq', 'image');
		} elseif ($rq == '' && $row->folder == '') {
			$this->assign('rq', 'folder');
		} elseif ($rq == '' && JRequest::getVar('folder') != '') {
			$this->assign('rq', 'image');
		} else {
			$this->assign('rq', $rq);
		}

		if (!isset($row->published)) {
			$published = 1;
		} else {
			$published = $row->published;
		}
		
		if($row->folder != '') {
			$folder_parts = explode('/', $row->folder);
			$folder_count = count($folder_parts);
			
			if($folder_count > 1) {
				$last = count($folder_parts) - 1;
				$ucookie = GalleryHelper::setUCookieParams($folder_parts[$last]);
			} else if($folder_count == 1) {
				$ucookie = GalleryHelper::setUCookieParams($folder_parts[0]);
			} else {
				$ucookie = GalleryHelper::setUCookieParams();
			}
		} else {
			$ucookie = GalleryHelper::setUCookieParams();
		}
		
		if($row->folder == '') {
 			$row->folder = $user->username . DS . $ucookie->folder;
 		}
		
		if($cfgParams->get('image_external')) {
			$bpath = $cookieParams->bpath . DS . $row->folder;
		} else {
			$bpath = COM_MEDIA_BASE . DS .  $row->folder;
		}
		
		$this->assign('bpath', $bpath);
		
		if(JFolder::exists($bpath)) {
			if(is_writable($bpath)) {
				$temp_path_writable = $bpath . " <br/><span class='xgallery-ok'>" . JText::_('GALLERY PATH WRITABLE') ."</span>";
			} else {
				$temp_path_writable = $bpath . " <br/><span class='xgallery-error'>" . JText::_('GALLERY PATH NOT WRITABLE') ."</span>";
			}
		} else {
			$temp_show_swfupload = false;
			$temp_path_writable = $bpath . " <br/><span class='xgallery-error'>" . JText::_('GALLERY PATH NOT CURRENTLY EXIST') ."</span>";
		}
				
		$this->assignRef('temp_path_writable', $temp_path_writable);
		$this->assignRef('temp_show_swfupload', $temp_show_swfupload);
		
		$this->assign('imagefiles', $this->get('ImageList'));
		
		if($temp_show_swfupload) {
			// SWFUpload Scripts
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/swfupload.js');
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/swfupload.queue.js');
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/fileprogress.js');
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/handlers.js');
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/chandlers.js');
			$doc->addScript(JURI::base(true).'/components/com_xgallery/assets/swfupload/swfupload.cookies.js');
			$doc->addStyleSheet(JURI::base(true).'/components/com_xgallery/assets/swfupload/default.css');
			
			//add the javascript to the head of the html document
			$swfUploadHeadJs = $this->get('SWFUploadScript'); 		
			$doc->addScriptDeclaration($swfUploadHeadJs);
		}

		
		
		$this->assignRef('published', JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $published));
		$this->assignRef('creation_date', JHTML::_('calendar', $row->creation_date, 'creation_date', 'creation_date'));
		$thumblocal = '';
		$thumb = '';
		$resetThumb = false;

		$folderlocal = '<button name="select-folder-link" id="select-folder-link" rel="folder">'.JText::_('SELECT FOLDER').'</button>';
		$this->assignRef('folderlocal', $folderlocal);
		$folder = '';
					
		if($newFolder != '') {
			$resetThumb = true;
			$folder = JRequest::getVar('folder').'<input type="hidden" name="folder" value="'.JRequest::getVar('folder').'" />';
		} elseif(!empty($row->folder) || $row->folder != '') {
			$resetThumb = true;
			$folder = $row->folder.'<input type="hidden" name="folder" value="'.$row->folder.'" />';				
		}
		$this->assignref('folder', $folder);
		
		$thumblocal = '<button name="select-thumbnail-link" id="select-thumbnail-link" rel="image">'.JText::_('SELECT THUMBNAIL').'</button>';
		$this->assignRef('thumblocal', $thumblocal);
		$thumb = '';
		$newThumb = JRequest::getVar('imgPath');
		if(!empty($newThumb) || $newThumb != '') {
			$collString = "file=".'/'.urlencode($newThumb)."&amp;tn=0";	
			$thumb = '<img src="'.JURI::root(true).'/components/com_xgallery/helpers/img.php?'.$collString.'" height="100px" />					
					<input type="hidden" name="thumb" value="'.JRequest::getVar('imgPath').'" />';
		} elseif(!empty($row->thumb) || $row->thumb != '') {
			$collString = "file=".'/'.urlencode($row->thumb)."&amp;tn=0";
			$thumb = '<img src="'.JURI::root(true).'/components/com_xgallery/helpers/img.php?'.$collString.'" height="100px" />
					<input type="hidden" name="thumb" value="'.$row->thumb.'" />';
		}
		$this->assignref('thumb', $thumb);
		
		$doc->addScript("components/com_xgallery/assets/xgallery-coll.js");

		if(!empty($id_coll_tmp)) {
			$row->delete($id_coll_tmp);
			$session->set('id_coll_tmp', '', 'xgallery');
		}
		
		parent::display($tpl);
	}
}