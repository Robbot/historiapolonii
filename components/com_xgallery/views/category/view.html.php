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
	
class XGalleryViewCategory extends JView {
	function display($tpl = null) {
		global $mainframe;
		
		$cookieParams = GalleryHelper::getCookieParams();
		
		$params =& $mainframe->getParams();
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		
		$collections = $this->get('data');
		$category = $this->get('category');

		$pagitems =& $this->get('pagination');
		$catSlug = $category->id . ':' . GalleryHelper::getCategorySlug();

		$app    =& JFactory::getApplication();
		$router =& $app->getRouter();
		$router->setVar( 'id', $catSlug );
		
		$subCategories = $this->get('subCategories');
		
		$compId = GalleryHelper::getComponentId();		
		$menuid = $this->get('MenuCategory');
		$menu = &JSite::getMenu();
		
		$catDisplay = $params->get('prm_cat_display_layout', $cfgParams->get('cfg_cat_display_layout', 'cat_table'));
		$collDisplay = $params->get('prm_cat_coll_display_layout', $cfgParams->get('cfg_cat_coll_display_layout', 'table'));
		
		if($params->get('prm_show_cat_desc', $cfgParams->get('cfg_show_cat_desc', 0))) {
			$categorydesc = $category->description;
		} else if($params->get('prm_show_cat_quick', $cfgParams->get('cfg_show_cat_quick', 0))) {
			$categorydesc = $category->quicktake;
		} else {
			$categorydesc = '';
		}
		
		
		/*$currentId = JRequest::getVar('Itemid', '');
				
		if(!isset($compId->id) || $currentId != $compId->id) {
			$compId->id = $currentId;
		}*/
		
		$xgcompId = GalleryHelper::getXGalleryItemId();
		$compId->id = $xgcompId->id;
		
		$div_width = intval(100 / intval($params->get('prm_cat_thumbs_per_row', $cfgParams->get('cfg_cat_thumbs_per_row', 4))));
		$browser = GalleryHelper::getBrowserType();
		if($browser['browser'] == 'IE') {
			$div_width = $div_width - 1;
		}
		
		$user =& JFactory::getUser();
			
		$document = &JFactory::getDocument();
		
		$currTitle = $document->getTitle();
		
		if(strtolower($currTitle) != strtolower($category->name)) {
			$document->setTitle($currTitle.' - '.$category->name);
		}
		$document->setMetaData('title', $category->name);
		
		$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/style.css');
		if($collDisplay == 'list' || $catDisplay == 'cat_list') {
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/list-style.css');
		}

		$metadesc = $category->metadesc;
		$metakey = $category->metakey;
		$metaauthor = $category->metaauthor;
		$metarobots = $category->metarobots;
		
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
		
		if($cfgParams->get('enable_rss', 0)) {
			$feed =  JURI::root()."index.php?option=com_xgallery&controller=feed&view=rss&id={$category->id}&format=feed";
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
		
		$this->assign('collections', $collections);
		$this->assignRef('subCategories', $subCategories);
		$this->assignRef('pagination', $pagitems);
		$this->assignRef('images', $images);
		$this->assignRef('category', $category);
		$this->assignRef('name', $user->name);
		$this->assignRef('comments', $comments);
		$this->assignRef('compid', $compId->id);
		$this->assign('div_width', $div_width);
		$this->assign('num_thumbcol', $params->get('prm_cat_thumbs_per_row', $cfgParams->get('cfg_cat_thumbs_per_row', 4)));		
		$this->assign('categorydesc', $categorydesc);
		$this->assign('moduleclass_sfx', $params->get('moduleclass_sfx', ''));
		
		// General
		$this->assign('cfg_show_cat_title', $params->get('prm_show_cat_title', $cfgParams->get('cfg_show_cat_title', 1)));
		$this->assign('cfg_show_cat_rss', $params->get('prm_show_cat_rss', $cfgParams->get('cfg_show_cat_rss', 1)));		

		// Category 		
		$this->assign('cat_show_subcat', $params->get('prm_cat_show_subcat', $cfgParams->get('cfg_cat_show_subcat', 1)));
		$this->assign('cat_show_cat_title', $params->get('prm_cat_show_cat_title', $cfgParams->get('cfg_cat_show_cat_title', 1)));		
		$this->assign('cat_display_layout', $catDisplay);
		$this->assign('cat_show_name', $params->get('prm_cat_show_name', $cfgParams->get('cfg_cat_show_name', 1)));
		$this->assign('cat_show_date', $params->get('prm_cat_show_date', $cfgParams->get('cfg_cat_show_date', 1)));
		$this->assign('cat_show_hits', $params->get('prm_cat_show_hits', $cfgParams->get('cfg_cat_show_hits', 1)));
		$this->assign('cat_show_quick', $params->get('prm_cat_show_quick', $cfgParams->get('cfg_cat_show_quick', 1)));
		$this->assign('cat_width', $params->get('prm_cat_width', $cfgParams->get('cfg_cat_width')));
		$this->assign('cat_height', $params->get('prm_cat_height', $cfgParams->get('cfg_cat_height')));
		
		// Collections
		$this->assign('coll_show_coll', $params->get('prm_cat_coll_show_colls', $cfgParams->get('cfg_cat_coll_show_colls', 1)));
		$this->assign('coll_display_layout', $collDisplay);
		$this->assign('coll_show_coll_title', $params->get('prm_cat_coll_show_coll_title', $cfgParams->get('cfg_cat_coll_show_coll_title', 1)));
		$this->assign('coll_width', $params->get('coll_show_coll_title', $cfgParams->get('coll_width')));
		$this->assign('coll_height', $params->get('coll_show_coll_title', $cfgParams->get('coll_height')));
		$this->assign('coll_show_name', $params->get('prm_cat_coll_show_name', $cfgParams->get('cfg_cat_coll_show_name', 1)));
		$this->assign('coll_show_date', $params->get('prm_cat_coll_show_date', $cfgParams->get('cfg_cat_coll_show_date', 1)));
		$this->assign('coll_show_hits', $params->get('prm_cat_coll_show_hits', $cfgParams->get('cfg_cat_coll_show_hits', 1)));
		$this->assign('coll_show_desc', $params->get('prm_cat_coll_show_quick', $cfgParams->get('cfg_cat_coll_show_quick')));
		
		parent::display($tpl);
	}
}