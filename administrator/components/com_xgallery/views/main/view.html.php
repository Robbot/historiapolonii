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
	
	class XGalleryViewMain extends JView {
		function display($tpl = null) {
			$xgsearch = $this->get('SearchPlugin');
			$xgfce = $this->get('XGalleryFCE');
			$xgscroller = $this->get('XGalleryJScroller');
			$xgmenu = $this->get('XGalleryMenu');
			$xgcollection = $this->get('XGalleryCollection');
			$shadowbox = $this->get('Shadowbox');
			$gallerylocation = $this->get('GalleryLocation');
			$newcollections = $this->get('NewCollection');
			$collections = $this->get('ViewedCollection');
			$info = $this->get('ComponentInfo');

			$this->assignRef( 'xgsearch', $xgsearch );
			$this->assignRef( 'xgfce', $xgfce );
			$this->assignRef( 'xgscroller', $xgscroller );
			$this->assignRef( 'xgmenu', $xgmenu );
			$this->assignRef( 'xgcollection', $xgcollection );
			$this->assignRef( 'info', $info);
			$this->assignRef( 'shadowbox', $shadowbox );
			$this->assignRef( 'gallerylocation', $gallerylocation );
			$this->assignRef( 'newcollections', $newcollections );
			$this->assignRef( 'collections', $collections );
			
			$doc =& JFactory::getDocument();
			$doc->addStyleSheet(JURI::base(true).'/components/com_xgallery/assets/style.css');
		
			parent::display($tpl);
		}
	}