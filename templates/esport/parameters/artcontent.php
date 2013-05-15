<?php

defined('JPATH_BASE') or die;

require_once JPATH_SITE.'/libraries/joomla/html/parameter/element.php';

class JElementArtContent extends JElement    
{
    var $_name = 'ArtContent';

    function fetchElement($name, $value, &$node, $control_name)
    {
        // Initialize field attributes.
        $text   = $node->attributes('text') ? $node->attributes('text') : '';
        $value  = $node->attributes('value') ? $node->attributes('value') : '';
        
        // get theme name
        $cid        = JRequest::getVar('cid', array(), 'method', 'array');
        $cid        = array(JFilterInput::clean(@$cid[0], 'cmd'));
        $template   = $cid[0];
        
        $dataFolder = JURI::root(true).'/templates/'. $template .'/data';
        $document =& JFactory::getDocument();

        // include js, css files to create modal window
        $pathToModalJs =  JURI::root(true).'/media/system/js/modal.js';
        $document->addScript($pathToModalJs);
        $pathToModalCss = JURI::root(true).'/media/system/css/modal.css';
        $document->addStyleSheet($pathToModalCss);
        
        // include js script - jquery  file
        $pathToJQuery =  JURI::root(true).'/templates/'. $template .'/jquery.js';
        $document->addScript($pathToJQuery);
        
        // include js script - loader file  
        $pathToLoader =  JURI::root(true).'/templates/'. $template .'/data/loader.js';
        $document->addScript($pathToLoader);

        return '<button class="modal" type="submit" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" >'. JText::_($text) .'</button>'
        .'<input type="hidden" id="dataFolder" value="'. $dataFolder .'">'
        .'<div id="log"></div>';
    }
}
