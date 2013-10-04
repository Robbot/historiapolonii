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
	
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xgallery'.DS.'tables');
	
class XGalleryViewMain extends JView {
	function display($tpl = null) {
		global $mainframe;
		
		$cookieParams = GalleryHelper::getCookieParams();
		
		$main =& JTable::getInstance('Category', 'Table');
		$main->load(1);
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
  		
		$params =& $mainframe->getParams();

		$headingName = $main->name;
		
		if($params->get('main_show_main_desc', 0)) {
			$maindesc = $main->description;
		} else if($params->get('main_show_main_quick', 0)) {
			$maindesc = $main->quicktake;
		} else {
			$maindesc = '';
		}
		
		$collections = $this->get('data');
		$subCategories = $this->get('subCategories');
		
		$catDisplay = $params->get('main_cat_display_layout', 'cat_table');
		$collDisplay = $params->get('main_coll_display_layout', 'table');
		$compId = GalleryHelper::getComponentId();
		$pagitems =& $this->get('pagination');	
		$this->get('Breadcrumbs');
		$div_width = intval(100 / intval($params->get('num_thumbcol', 4)));
		$browser = GalleryHelper::getBrowserType();
		if($browser['browser'] == 'IE') {
			$div_width = $div_width - 1;
		}
		
		$document = &JFactory::getDocument();
		
		$currTitle = $document->getTitle();
		$document->setTitle($currTitle.' - '.$headingName);
		$document->setMetaData('title', $headingName);
		
		$metadesc = $cfgParams->get('metadesc', '');
		$metakey = $cfgParams->get('metakey', '');
		$metaauthor = $cfgParams->get('metaauthor', '');
		$metarobots = $cfgParams->get('metarobots', '');
		
		if ($metadesc) {
			$document->setDescription( $metadesc );
		}
		
		if ($metakey) {
			$document->setMetadata('keywords', $metakey);
		}
		
		if ($metaauthor) {
			$document->setMetadata('author', $metaauthor);
		}
		
		if ($metarobots) {
			$document->setMetadata('robots', $metarobots);
		}
		
		$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/style.css');
		if($catDisplay == 'cat_list' || $collDisplay == 'list') {
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/list-style.css');
		}
		
		if($cfgParams->get('enable_rss', 0)) {
			$feed = JURI::root()."index.php?option=com_xgallery&controller=feed&view=rss&id=1&format=feed";
			$rss = array(
						'type' 	=>  'application/rss+xml',
						'title'	=>	'RSS 1.0');
		
			$atom = array(
						'type' 	=>  'application/atom+xml',
						'title'	=>	'Atom');
				
			$document->addHeadLink(JRoute::_($feed.'&type=rss'), 'alternate', 'rel', $rss);
			$document->addHeadLink(JRoute::_($feed.'&type=atom'), 'alternate', 'rel', $atom);
			$this->assign('enable_rss', $cfgParams->get('enable_rss'));
		} else {
			$this->assign('enable_rss', $cfgParams->get('enable_rss'));
		}
		
		$user =& JFactory::getUser();	
				
		$this->assignRef('subCategories', $subCategories);
		$this->assignRef('heading', $headingName);		
		$this->assignRef('pagination', $pagitems);
		$this->assignRef('div_width', $div_width);
		$this->assignRef('num_thumbcol', $cfgParams->get('num_thumbcol', 4));
		$this->assignRef('display_layout', $collDisplay);
		$this->assignRef('cat_display_layout', $catDisplay);
		$this->assignRef('compid', $compId->id);
		$this->assign('main', $main);
		$this->assign('collections', $collections);
		$this->assign('maindesc', $maindesc);
		$this->assign('rWidth', $params->get('rWidth_admin'));
		$this->assign('rHeight', $params->get('rHeight_admin'));		
		
		$this->assign('show_main_title', $params->get('main_show_main_title', 1));
		$this->assign('show_rss_feed', $params->get('main_show_main_rss', 1));
		$this->assign('show_cat_title', $params->get('main_show_cat_title', 1));
		$this->assign('show_coll_title', $params->get('main_show_coll_title', 1));
		
		$this->assign('show_subcat', $params->get('main_show_subcat', 1));
		$this->assign('cat_show_name', $params->get('main_show_cat_name', 1));
		$this->assign('cat_show_date', $params->get('main_show_cat_date', 1));
		$this->assign('cat_show_hits', $params->get('main_show_cat_hits', 1));
		$this->assign('cat_show_quick', $params->get('main_show_cat_quick', 1));
				
		$this->assign('show_colls', $params->get('main_show_colls', 1));
		$this->assign('show_name', $params->get('main_show_coll_name', 1));
		$this->assign('show_date', $params->get('main_show_coll_date', 1));
		$this->assign('show_hits', $params->get('main_show_coll_hits', 1));
		$this->assign('show_quick', $params->get('main_show_coll_quick', 0));	
		
		parent::display($tpl);
	}
}