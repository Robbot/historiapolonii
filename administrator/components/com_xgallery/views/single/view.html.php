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
	
class XGalleryViewSingle extends JView {
	function display($tpl = null) {
		//Load pane behavior
		jimport('joomla.html.pane');
		
		$session =& JFactory::getSession();
		$id_cat_tmp = $session->get('id_cat_tmp', '', 'xgallery');	
		
		if(empty($id_cat_tmp)) {
			$row =& JTable::getInstance('Category', 'Table');
			$id = JRequest::getVar('id');
			$row->load($id);		
		} else {
			$row =& JTable::getInstance('CategoryTmp', 'Table');
			$row->load($id_cat_tmp);
			$id = $row->id;
		}
		
		if(!isset($row->ordering)) {
			$row->ordering = '';
		}
		
		$this->assignRef('row', $row);
		$editor =& JFactory::getEditor();
		$this->assignRef('editor', $editor);
		$thumb = '';
		$thumblocal = '';
		$cookieParams = GalleryHelper::getCookieParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		$pane = & JPane::getInstance('sliders');	
		
		if($id == 1) {
			$pids = '';
		} else {
			$pids = fetchSelectList($id, $row->pid, 'pid');
		}

		$rq = JRequest::getVar('reqType', '');
		if ($rq == '' && $row->thumb == '') {
			$this->assign('rq', 'image');
		} else {
			$this->assign('rq', $rq);
		}
		
		$thumblocal = '<button name="select-thumbnail-link" id="select-thumbnail-link" rel="image">'.JText::_('SELECT THUMBNAIL').'</button>';
		$newThumb = JRequest::getVar('imgPath');
			
		if(!empty($newThumb) || $newThumb != '') {
			$collString = "file=".'/'.urlencode($newThumb)."&amp;tn=0";
			$thumb = '<img src="'.JURI::root(true).'/components/com_xgallery/helpers/img.php?'.$collString.'" height="100px" />
					<input type="hidden" name="thumb" value="'.$newThumb.'" />';
		} elseif(!empty($row->thumb) || $row->thumb != '') {
			$collString = "file=".'/'.urlencode($row->thumb)."&amp;tn=0";
			$thumb = '<img src="'.JURI::root(true).'/components/com_xgallery/helpers/img.php?'.$collString.'" height="100px" />
					<input type="hidden" name="thumb" value="'.$row->thumb.'" />';
		}				
		
		if (!isset($row->published)) {
			$published = 1;
		} else {
			$published = $row->published;
		}
		
		$this->assignRef('temp_path_writable', $temp_path_writable);
		$this->assignRef('thumb', $thumb);
		$this->assignRef('thumblocal', $thumblocal);
		$this->assignRef('pid', $pids);
		$this->assignRef('pane', $pane);
		$this->assignRef('published', JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $published));
		$this->assignRef('creation_date', JHTML::_('calendar', $row->creation_date, 'creation_date', 'creation_date'));

		$doc =& JFactory::getDocument();
		$doc->addScript("components/com_xgallery/assets/xgallery-cat.js");
		
		if(!empty($id_cat_tmp)) {
			$row->delete($id_cat_tmp);
			$session->set('id_cat_tmp', '', 'xgallery');
		}
		
		parent::display($tpl);
	}
}