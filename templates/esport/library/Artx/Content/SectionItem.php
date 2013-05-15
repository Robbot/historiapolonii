<?php
defined('_JEXEC') or die;

ArtxLoadClass("Artx_Content_Item");

class ArtxContentSectionItem extends ArtxContentItem
{
    function ArtxContentSectionItem(&$component, &$componentParams, &$article, &$articleParams)
    {
        parent::ArtxContentItem($component, $componentParams, $article, $articleParams);
        $this->section = $this->params->get('show_section') && $this->_article->sectionid
                           && isset($this->_article->section->title)
                           ? $this->_article->section->title : '';
    }
}
