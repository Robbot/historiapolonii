<?php
/**
 * JFUploader 3.1 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2013 TinyWebGallery
 * written by Michael Dempfle
 * 
 * @license GNU / GPL   
 *
 * For the latest version please go to http://jfu.tinywebgallery.com
**/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class TOOLBAR_jfuploader {

function _EDIT() {

  JToolBarHelper::title( JText::_( 'Contact' ) .': <small><small>[ '. $text .' ]</small></small>', 'generic.png' );
  JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
						JToolBarHelper::save('saveConfig');
						JToolBarHelper::cancel('config');
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
        
function _USER() {
						JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
            JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
            JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'plugins', 'plugins','plugins',JText::_('T_PLUGINS'),false);
					  JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
        
function _HELP() {
            JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
						JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
            JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'plugins', 'plugins','plugins',JText::_('T_PLUGINS'),false);
					  JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);		
        }     
        
function _PLUGINS() {
            JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
						JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
            JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);		
        }    
        
function _UPLOAD() {
						JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
            JToolBarHelper::custom( 'config', 'config','config',JText::_('T_CONFIG'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'plugins', 'plugins','plugins',JText::_('T_PLUGINS'),false);
					  JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);
				}

function _LIST() {
						JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );

            JToolBarHelper::custom( 'upload', 'upload','upload',JText::_('T_UPLOAD'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'user', 'user','user',JText::_('T_USER'),false);
						JToolBarHelper::spacer();
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::custom( 'plugins', 'plugins','plugins',JText::_('T_PLUGINS'),false);
					  JToolBarHelper::spacer();
            JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::addNew('newConfig');
						JToolBarHelper::editList('edit');
						JToolBarHelper::custom( 'copyConfig', 'copy', 'copy', JText::_('T_COPY'), false );
						JToolBarHelper::save('saveMainConfig');
						JToolBarHelper::deleteList('','deleteConfig');
						JToolBarHelper::cancel();
						JToolBarHelper::spacer();
						JToolBarHelper::spacer();
						JToolBarHelper::help('tfu',true);	
        }
               
function _DEFAULT() {
						JToolBarHelper::title('JFUploader', 'jfu_toolbar_title' );
            JToolBarHelper::help('tfu',true);	
						JToolBarHelper::cancel();
        }
}
?>