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

function xgalleryBuildRoute(&$query) {
	$segments = array();

	if(isset($query['view']) && $query['view'] == 'single') {
		$mId	=  $query['id'];

		if(isset($query['catid'])) {
			$mCatid = $query['catid'];
		} else {
			$mCatid = JRequest::getVar('catid', '');

			if($mCatid == '') {
				$cid = GalleryHelper::getCategory($mId);
				$mCatid = $cid->cid . ":" . GalleryHelper::getCategorySlug();
			}
		}		
		
		if(!strpos($mId, ":")) {
			$mId = $mId . ":" . GalleryHelper::getCollectionSlug();
		}

		$segments[] = $mCatid;
		$segments[] = $mId;

		if(isset($query['catid'])) {
			unset($query['catid']);
		}

		unset( $query['id'] );	
	} else {
		if(isset( $query['catid'] )) {
			$segments[] = $query['catid'];
			unset( $query['catid'] );
		};
	
		if( isset($query['id']) ) {
			$segments[] = $query['id'];
			unset( $query['id'] );
		};	

	}

	unset( $query['view'] );
	return $segments;	
}

function xgalleryParseRoute($segments) {
	$vars = array();
	$menu =& JMenu::getInstance('site');
	$item =& $menu->getActive();
	
	// Count segments
	$count = count( $segments );

	//Handle View and Identifier
	switch( $item->query['view'] ) {
		case 'categories':
			if($count == 1) {
				$vars['view'] = 'category';
			}
			
			if($count == 2) {
				$vars['view'] = 'single';
			}
			
			$id = explode( ':', $segments[$count-1] );
			$vars['id'] = (int) $id[0];
			break;
		case 'category':
			$id = explode( ':', $segments[$count-1] );
			$vars['id']   = (int) $id[0];
			
			if($count == 1) {
				$vars['view'] = 'category';
			}
			
			if($count == 2) {
				$vars['view'] = 'single';
			}
			break;
		case 'single':
			$id = explode( ':', $segments[$count-1] );
			$vars['id']   = (int) $id[0];
			
			if($count == 1) {
				$vars['view'] = 'category';
			}
			
			if($count == 2) {
				$vars['view'] = 'single';
			}
			break;
		case 'main':
			$id = explode( ':', $segments[$count-1] );
			$vars['id']   = (int) $id[0];
			
			if($count == 1) {
				$vars['view'] = 'category';
			}
			
			if($count == 2) {
				$vars['view'] = 'single';
			}			
			break;
	}
	return $vars;	
}