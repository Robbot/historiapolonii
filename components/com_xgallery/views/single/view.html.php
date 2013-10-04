<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component xgallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xgallery'.DS.'tables');

class XGalleryViewSingle extends JView {
	function display($tpl = null) {
		global $mainframe;
				
		$cookieParams = GalleryHelper::getCookieParams();
		
		$component = JComponentHelper::getComponent( 'com_xgallery' );
  		$cfgParams = new JParameter( $component->params );
		  
		$pathway   =& $mainframe->getPathway();
		$params =& $mainframe->getParams();	
		$collection = $this->get('data'); // must be called first

		$display = $params->get('coll_display_layout', $cfgParams->get('display_layout', 'shadowbox'));		
		$this->get('Breadcrumbs');
		
		$images = array();
		$images = $this->get('images');
		
		$compId = GalleryHelper::getComponentId();
		$menuid = $this->get('MenuCategories');
		$menu = &JSite::getMenu();
		
		if($compId->id == $this->get('ComponentId')) {
			$isGal = true;
		} else { 
			$isGal = false;
		}
		
		$active	= $menu->getActive();
		
		$pagitems =& $this->get('pagination');
		$pagelinks = $pagitems->getPagesLinks();

		$app    =& JFactory::getApplication();
		$router =& $app->getRouter();
		$router->setVar( 'catid', $collection[0]->cid . ':' . GalleryHelper::getCategorySlug());
		$router->setVar( 'id', $collection[0]->id . ':' . GalleryHelper::getCollectionSlug());
		
		$date = JHTML::Date($collection[0]->creation_date);		
		$user =& JFactory::getUser();			
		$document = &JFactory::getDocument();
		
		$catName = $this->get('CatName');

		$currTitle = $document->getTitle();
		$document->setTitle($currTitle.' - '.$catName[0]->name.' - '.$collection[0]->name);
		$document->setMetaData('title', $collection[0]->name);
		
		if($params->get('prm_coll_show_coll_desc', $cfgParams->get('cfg_coll_show_coll_desc', 0))) {
			$collectiondesc = $collection[0]->description;
		} else if($params->get('prm_coll_show_coll_quick', $cfgParams->get('cfg_coll_show_coll_quick', 0))) {
			$collectiondesc = $collection[0]->quicktake;
		} else {
			$collectiondesc = '';
		}
		
		if($params->get('prm_coll_show_coll_sub', $cfgParams->get('cfg_coll_show_coll_sub', 0))) {
			
			$coll_sub = $collection[0]->quicktake;
		} else {
			$coll_sub = '';
		}
		
		$metadesc = $collection[0]->metadesc;
		$metakey = $collection[0]->metakey;
		$metaauthor = $collection[0]->metaauthor;
		$metarobots = $collection[0]->metarobots;
		
		if($cfgParams->get('include_metakey', 0)) {
			if($metakey && $catName[0]->metakey) {
				$metakey = $metakey . ', ' . $catName[0]->metakey;
			} elseif($catName[0]->metakey) {
				$metakey = $catName[0]->metakey;
			}
		} 
		
		if($cfgParams->get('include_metadesc', 0)) {
			if($metadesc && $catName[0]->metadesc) {
				$metadesc = $catName[0]->metadesc . ' ' . $metadesc;
			} elseif($catName[0]->metakey) {
				$metadesc = $catName[0]->metadesc;
			}
		}
		
		
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
				
		jimport('joomla.filesystem.file');

		if($params->get('col_enable_lightbox', $cfgParams->get('enable_lightbox', 1))) {
			if($cfgParams->get('shadowbox_type', 'shadowbox') == 'shadowbox') {
				$shadowbox = 'shadowbox['.$collection[0]->name.'];player=img';
			} else {
				$shadowbox = 'lightbox['.$collection[0]->name.'];player=img';
			}
			$this->assign('shadowbox', $shadowbox);
		} else {
			$this->assign('shadowbox', '');
		}
			
		//$this->assign('display_comments', $params->get('display_comments', '1'));
		$this->assign('display_layout', $display);
		$this->assign('filter_images', $cfgParams->get('filter_images', 1));		
		$this->assign('image_path', $cfgParams->get('image_path', 'images/stories'));
		$this->assign('image_external', $cfgParams->get('image_external', 0));
		$this->assign('collection', $collection);
		$this->assign('coll_sub_enabled', $params->get('prm_coll_show_coll_sub', $cfgParams->get('cfg_coll_show_coll_sub', 0)));
		$this->assign('coll_sub', $coll_sub);
		$this->assign('enable_coll_page', $params->get('col_enable_coll_page', $cfgParams->get('enable_coll_page', 1)));
		$this->assignRef('pagination', $pagitems);
		$this->assign('pagelinks', $pagelinks);
		$this->assign('isgal', $isGal);
		$this->assign('collectiondesc', $collectiondesc);
		
		$width = $params->get('col_width', $cfgParams->get('coll_width'));
		$height = $params->get('col_height', $cfgParams->get('coll_height'));
		
		$this->assign('date', $date);
		$this->assign('name', $user->name);
		$this->assignRef('router', $router);
		$this->assignRef('compid', $compId);
		$this->assign('moduleclass_sfx', $params->get('moduleclass_sfx', ''));
		$this->assign('rWidth', $width);
		$this->assign('rHeight', $height);
		$this->assign('enable_watermark', $cfgParams->get('enable_watermark', 0));
		$this->assign('watermark_path', $cfgParams->get('watermark_path', ''));
		$this->assign('wm_h_position', $cfgParams->get('wm_h_position', 'r'));
		$this->assign('wm_v_position', $cfgParams->get('wm_v_position', 'b'));
		$this->assign('wm_opacity', $cfgParams->get('wm_opacity', 100));
		
		$this->assign('coll_show_coll_title', $params->get('col_show_coll_title', $cfgParams->get('coll_show_coll_title', 1)));
		$this->assign('coll_show_thumb_name', $params->get('prm_coll_show_tname', $cfgParams->get('cfg_coll_show_tname', 0)));
		$this->assign('coll_show_name', $params->get('prm_coll_show_name', $cfgParams->get('cfg_coll_show_name', 1)));
		$this->assign('coll_show_date', $params->get('prm_coll_show_date', $cfgParams->get('cfg_coll_show_date', 1)));
		$this->assign('coll_show_hits', $params->get('prm_coll_show_hits', $cfgParams->get('cfg_coll_show_hits', 1)));
		
		if($cfgParams->get('load_jquery', 0)) {
			$document->addScript(JURI::base(true).'/components/com_xgallery/js/jquery.js');
		}
		
		if ($display == 'shadowbox') {
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/style.css');				
			
			$this->assignRef('images', $images);
			
			$div_width = intval(100 / intval($params->get('col_num_thumbcol', $cfgParams->get('num_thumbcol', 4))));
			$browser = GalleryHelper::getBrowserType();
			if($browser['browser'] == 'IE') {
				$div_width = $div_width - 1;
			}
			
			$this->assignRef('div_width', $div_width);
			$this->assignRef('num_thumbcol', $params->get('col_num_thumbcol', $cfgParams->get('num_thumbcol', 4)));
			
		} else {
			$document->addScript( JURI::base(true).'/components/com_xgallery/js/jquery.galleriffic.js' );
			$document->addScript( JURI::base(true).'/components/com_xgallery/js/jquery.opacityrollover.js' );
			$document->addScript( JURI::base(true).'/components/com_xgallery/js/jush.js' );
			
			if((bool)$cfgParams->get('galleriffic_enableHistory', 0)) {
				$document->addScript( JURI::root() . '/components/com_xgallery/js/jquery.history.js' );
			}
			
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/style.css');
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/basic.css');			
			$document->addStyleSheet(JURI::base(true).'/components/com_xgallery/css/galleriffic-2.css');			
			
			$this->assignRef('images', $images);
			
			//$startLimit = $this->get('LimitStart');
			//$maxWidth = $params->get('col_width_max', $cfgParams->get('coll_width_max', 500));
			//$maxHeight = $params->get('col_height_max', $cfgParams->get('coll_height_max', 500));

			$this->assignRef('num_thumbcol', $params->get('col_num_thumbcol', $cfgParams->get('num_thumbcol', 4)));
				
			$enableTopPager = ((bool)$cfgParams->get('galleriffic_enableTopPager', 1)) ? 'true' : 'false';
			$enableBottomPager = ((bool)$cfgParams->get('galleriffic_enableBottomPager', 1)) ? 'true' : 'false';
			$renderSSControls = ((bool)$cfgParams->get('galleriffic_renderSSControls', 1)) ? 'true' : 'false';
			$renderNavControls = ((bool)$cfgParams->get('galleriffic_renderNavControls', 1)) ? 'true' : 'false';
			$enableKeyboardNavigation = ((bool)$cfgParams->get('galleriffic_enableKeyboardNavigation', 1)) ? 'true' : 'false';
			$enableHistory = ($cfgParams->get('galleriffic_enableHistory', 0)) ? 'true' : 'false';
			$autoStart = ($cfgParams->get('galleriffic_autoStart', 0)) ? 'true' : 'false';
			$syncTransitions = ((bool)$cfgParams->get('galleriffic_syncTransitions', 1)) ? 'true' : 'false';
			$jsScript = "
				jQuery(document).ready(function($) {
				// We only want these styles applied when javascript is enabled
				$('div.navigation').css({'width' : '".(int)$cfgParams->get('galleriffic_navwidth', 700)."px'});
				$('div.content').css('display', 'block');

				// Initially set opacity on thumbs and add
				// additional styling for hover effect on thumbs
				var onMouseOutOpacity = 0.67;
				$('#thumbs ul.thumbs li').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				
				// Initialize Advanced Galleriffic Gallery
				var gallery = $('#thumbs').galleriffic({
					delay:                     ".(int)$cfgParams->get('galleriffic_delay', 2500).",
					numThumbs:                 ".(int)$cfgParams->get('galleriffic_numthumbs', 20).",
					preloadAhead:              ".(int)$cfgParams->get('galleriffic_preloadAhead', 10).",
					enableTopPager:            ".$enableTopPager.",
					enableBottomPager:         ".$enableBottomPager.",
					maxPagesToShow:            ".(int)$cfgParams->get('galleriffic_maxPagesToShow', 7).",
					imageContainerSel:         '#slideshow',
					controlsContainerSel:      '#controls',
					captionContainerSel:       '#caption',
					loadingContainerSel:       '#loading',
					renderSSControls:          ".$renderSSControls.",
					renderNavControls:         ".$renderNavControls.",
					playLinkText:              '".JTEXT::_('PLAY LINK TEXT')."',
					pauseLinkText:             '".JTEXT::_('PAUSE LINK TEXT')."',
					prevLinkText:              '".JTEXT::_('PREV LINK TEXT')."',
					nextLinkText:              '".JTEXT::_('NEXT LINK TEXT')."',
					nextPageLinkText:          '".JTEXT::_('NEXT PAGE LINK TEXT')."',
					prevPageLinkText:          '".JTEXT::_('PREV PAGE LINK TEXT')."',
					enableHistory:             ".$enableHistory.",
					enableKeyboardNavigation:  ".$enableKeyboardNavigation.",
					autoStart:                 ".$autoStart.",
					syncTransitions:           ".$syncTransitions.",
					defaultTransitionDuration: ".(int)$cfgParams->get('galleriffic_defaultTransitionDuration', 900).",
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of $('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						this.fadeTo('fast', 1.0);
					}
				});
				});";
			
			$document->addScriptDeclaration($jsScript);	
			
			$cssStyle = "
				.noscript { display: none; }
			
				#thumbContainer img.thumbs {
					height:{$height}px;
					width:{$width}px; 
				}
			";
			
			$document->addStyleDeclaration($cssStyle);
		}
	
		parent::display($tpl);
	}
}