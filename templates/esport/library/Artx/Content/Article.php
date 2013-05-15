<?php
defined('_JEXEC') or die;

ArtxLoadClass("Artx_Content_ArticleBase");

class ArtxContentArticle extends ArtxContentArticleBase
{
    
    /**
     * @access public
     */
    var $showPageHeading;

    /**
     * @access public
     */
    var $pageHeading;

    /**
     * @access public
     */
    function ArtxContentArticle(&$component, &$componentParams, &$article, &$articleParams)
    {
        parent::ArtxContentArticleBase($component, $componentParams, $article, $articleParams);
        $this->showPageHeading = $this->_component->params->get('show_page_title', 1)
                                   && $this->_component->params->get('page_title') != $this->_article->title;
        $this->pageHeading = $this->_component->params->get('page_title');
        $this->showPdfIcon = $this->showPdfIcon && !$this->_component->print;
        $this->showEmailIcon = $this->showEmailIcon && !$this->_component->print;
        $this->showEditIcon = $this->showEditIcon && !$this->_component->print;
    }

    /**
     * @access public
     */
    function printIcon()
    {
        return JHTML::_($this->_component->print ? 'icon.print_screen' : 'icon.print_popup',
                        $this->_article, $this->params, $this->_component->access);
    }

    /**
     * @access public
     */
    function urlInfo()
    {
        return '<a href="http://' . $this->_component->escape($view->_article->urls) . '" target="_blank">'
                 . $this->_component->escape($view->_article->urls) . '</a>';
    }

    /**
     * @access public
     */
    function toc()
    {
        return isset($this->_article->toc) ? $this->_article->toc : '';
    }
}
