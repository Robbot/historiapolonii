<?php
/**
* @version		$Id: view.html.php 11307 2008-11-24 00:21:48Z ian $
* @package		Joomla
* @subpackage	Media
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Media component
 *
 * @static
 * @package		Joomla
 * @subpackage	Media
 * @since 1.0
 */
class XGalleryViewMediaList extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		// Do not allow cache
		JResponse::allowCache(false);
		$cookieParams = GalleryHelper::getCookieParams();
		$style = $mainframe->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

		JHTML::_('behavior.mootools');

		$document = &JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/assets/medialist-'.$style.'.css');
		//$document->addScript('/components/com_xgallery/js/jquery.js');
		//$document->addScript('/plugins/system/shadowbox/shadowbox.js');
		//$document->addStyleSheet('/plugins/system/shadowbox/shadowbox.css');		
		/*$document->addScriptDeclaration('
		jQuery.noConflict(); 
		Shadowbox.init({ players: ["img"], adapter: "base" });');*/
		
		
		$document->addScriptDeclaration("
		window.addEvent('domready', function() {
			window.top.document.updateUploader && window.top.document.updateUploader();
			/*$$('a.img-preview').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					window.top.document.preview.fromElement(el);
				});
			});*/
		});");
		
		$params = &JComponentHelper::getParams( 'com_xgallery' );
		$this->assign('rWidth', $params->get('rWidth_admin', 75));
		$this->assign('rHeight', $params->get('rHeight_admin', 75));
		$this->assign('baseURL', JURI::root());
		$this->assignRef('images', $this->get('images'));
		$this->assignRef('documents', $this->get('documents'));
		$this->assignRef('folders', $this->get('folders'));
		$this->assignRef('state', $this->get('state'));

		parent::display($tpl);
	}

	function setFolder($index = 0)
	{
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject;
		}
	}

	function setImage($index = 0)
	{
		if (isset($this->images[$index])) {
			$this->_tmp_img = &$this->images[$index];
		} else {
			$this->_tmp_img = new JObject;
		}
	}

	function setDoc($index = 0)
	{
		if (isset($this->documents[$index])) {
			$this->_tmp_doc = &$this->documents[$index];
		} else {
			$this->_tmp_doc = new JObject;
		}
	}
}
