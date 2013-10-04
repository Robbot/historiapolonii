<?php
defined('_JEXEC') or die('Restricted access');	
	
jimport('joomla.application.component.view');
require_once(JApplicationHelper::getPath('helper'));

class JMovieViewCategory extends JView {
	function display($tpl = null) {
		$row =& JTable::getInstance('category', 'Table');
		$id = JRequest::getVar('id');
		$row->load($id);
		$this->assignRef('row', $row);
		$editor =& JFactory::getEditor();
		$this->assignRef('editor', $editor);
		$path = COM_MEDIA_BASE . DS . $row->thumb;
		$thumb = '';
		$thumb_writable = '';
		
		if(JMovieMovieHelper::checkImageUploadPath(COM_MEDIA_BASE)) {
			$thumb_writable = "<br/>".COM_MEDIA_BASE." <span style='color:#007700'>is writable</span>";
		} else {
			$thumb_writable = "<br/>".COM_MEDIA_BASE." <span style='color:#DD0000'>is not writable</span>";
		}
		
		
		if($id == 1) {
			$pids = '';
		} else {
			$pids = fetchSelectList($id, $row->pid, 'pid');
		}

		if($row->thumb != '') {
			$thumb = "<img src='". COM_MEDIA_BASEURL . '/' . $row->thumb ."' id='thumbnail' style='height:50px;' />";
		}
				
		if (!isset($row->published)) {
			$published = 1;
		} else {
			$published = $row->published;
		}
		$this->assignref('thumb', $thumb);
		$this->assignref('thumb_writable', $thumb_writable);
		$this->assignRef('pid', $pids);
		$this->assignRef('published', JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $published));
		$this->assignRef('creation_date', JHTML::_('calendar', $row->creation_date, 'creation_date', 'creation_date'));
							
		parent::display($tpl);
	}
}