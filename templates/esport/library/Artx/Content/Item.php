<?php
defined('_JEXEC') or die;

ArtxLoadClass("Artx_Content_ArticleBase");

class ArtxContentItem extends ArtxContentArticleBase
{
    /**
     * @access public
     */
    var $isPublished;

    /**
     * @access public
     */
    var $showReadmore;

    /**
     * @access public
     */
    function ArtxContentItem(&$component, &$componentParams, &$article, &$articleParams)
    {
        parent::ArtxContentArticleBase($component, $componentParams, $article, $articleParams);
        $this->isPublished = 0 != $this->_article->state;
        $this->showReadmore = $this->params->get('show_readmore') && $this->_article->readmore;
    }

    /**
     * Returns decoration for unpublished articles.
     *
     * Together with endUnpublishedArticle() this function decorates 
     * the unpublished article with <div class="system-unpublished">...</div>.
     * By default, this decoration is applied only to articles in lists.
     *
     * @access public
     */
    function beginUnpublishedArticle() { return '<div class="system-unpublished">'; }

    /**
     * @access public
     */
    function endUnpublishedArticle() { return '</div>'; }

    /**
     * @access public
     */
    function urlInfo()
    {
        return '<a href="http://' . $view->_article->urls . '" target="_blank">'
                 . $this->_component->escape($view->_article->urls) . '</a>';
    }

    /**
     * @access public
     */
    function readmore()
    {
        $readmore = $this->_article->readmore_register
               ? JText::_('Register to read more...')
               : ($this->params->get('readmore')
                    ? $this->params->get('readmore')
                    : JText::sprintf('Read more...'));
        return '<p class="readmore">' . artxLinkButton(array(
            'classes' => array('a' => 'readon'),
            'link' => $this->_article->readmore_link,
            'content' => str_replace(' ', '&#160;', $readmore))) . '</p>';
    }
}
