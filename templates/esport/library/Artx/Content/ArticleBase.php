<?php
defined('_JEXEC') or die;

/**
 * @abstract
 */
class ArtxContentArticleBase
{
    /**
     * @access protected
     */
    var $_component;

    /**
     * @access protected
     */
    var $_componentParams;

    /**
     * @access protected
     */
    var $_article;

    /**
     * @access public
     */
    var $params;

    /**
     * @access public
     */
    var $canEdit;

    /**
     * @access public
     */
    var $title;

    /**
     * @access public
     */
    var $titleVisible;

    /**
     * @access public
     */
    var $titleLink;

    /**
     * @access public
     */
    var $showPdfIcon;

    /**
     * @access public
     */
    var $showPrintIcon;

    /**
     * @access public
     */
    var $showEmailIcon;

    /**
     * @access public
     */
    var $showEditIcon;

    /**
     * @access public
     */
    var $showAuthor;

    /**
     * @access public
     */
    var $showCreateDate;

    /**
     * @access public
     */
    var $showModifyDate;

    /**
     * @access public
     */
    var $showCategory;

    /**
     * @access public
     */
    var $category;

    /**
     * @access public
     */
    var $categoryLink;

    /**
     * @access public
     */
    var $showSection;

    /**
     * @access public
     */
    var $section;

    /**
     * @access public
     */
    var $sectionLink;

    /**
     * @access public
     */
    var $showIntro;

    /**
     * @access public
     */
    var $showUrl;

    /**
     * @access public
     */
    function ArtxContentArticleBase(&$component, &$componentParams, &$article, &$articleParams)
    {
        // Initialization:
        $this->_component = &$component;
        $this->_componentParams = &$componentParams; 
        $this->_article = &$article;
        $this->params = &$articleParams;

        // Configuring properties:
        $this->canEdit = $this->_component->user->authorize('com_content', 'edit', 'content', 'all')
                           || $this->_component->user->authorize('com_content', 'edit', 'content', 'own');
        $this->title = $this->_article->title;
        $this->titleVisible = $this->params->get('show_title') && strlen($this->title);
        $this->titleLink = $this->params->get('link_titles') && '' != $this->_article->readmore_link
                             ? $this->_article->readmore_link : '';
        $this->showCreateDate = $this->params->get('show_create_date');
        $this->showModifyDate = 0 != intval($this->_article->modified) && $this->params->get('show_modify_date');
        $this->showAuthor = $this->params->get('show_author') && '' != $this->_article->author;
        $this->showPdfIcon = $this->params->get('show_pdf_icon');
        $this->showPrintIcon = $this->params->get('show_print_icon');
        $this->showEmailIcon = $this->params->get('show_email_icon');
        $this->showEditIcon = $this->canEdit;
        $this->showUrl = $this->params->get('show_url') && $this->_article->urls;
        $this->showIntro = $this->params->get('show_intro');
        $this->showSection = $this->params->get('show_section') && $this->_article->sectionid && isset($this->_article->section);
        $this->section = $this->showSection ? $this->_article->section : '';
        $this->sectionLink = ($this->showSection && $this->params->get('link_section'))
                               ? JRoute::_(ContentHelperRoute::getSectionRoute($this->_article->sectionid)) : '';
        $this->showCategory = $this->params->get('show_category') && $this->_article->catid;
        $this->category = $this->showCategory ? $this->_article->category : '';
        $this->categoryLink = ($this->showCategory && $this->params->get('link_category'))
                                ? JRoute::_(ContentHelperRoute::getCategoryRoute($this->_article->catslug, $this->_article->sectionid)) : '';
    }

    /**
     * @see $createDateVisible
     * @access public
     */
    function createDateInfo()
    {
        return JHTML::_('date', $this->_article->created, JText::_('DATE_FORMAT_LC2'));
    }

    /**
     * @see $modifyDateVisible
     * @access public
     */
    function modifyDateInfo()
    {
        return JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->_article->modified, JText::_('DATE_FORMAT_LC2')));
    }

    /**
     * @see $author
     * @access public
     */
    function authorInfo()
    {
        $author = $this->_article->created_by_alias ? $this->_article->created_by_alias : $this->_article->author;
        return JText::sprintf('Written by', $this->_component->escape($author));
    }

    /**
     * @see $pdfIconVisible
     * @access public
     */
    function pdfIcon()
    {
        return JHTML::_('icon.pdf', $this->_article, $this->params, $this->_component->access);
    }

    /**
     * @see $emailIconVisible
     * @access public
     */
    function emailIcon()
    {
        return JHTML::_('icon.email', $this->_article, $this->params, $this->_component->access);
    }

    /**
     * @see $editIconVisible
     * @access public
     */
    function editIcon()
    {
        return JHTML::_('icon.edit', $this->_article, $this->params, $this->_component->access);
    }

    /**
     * @see $printIconVisible
     * @access public
     */
    function printIcon()
    {
        return JHTML::_('icon.print_popup', $this->_article, $this->params, $this->_component->access);
    }

    /**
     * @access public
     */
    function articleSeparator() { return '<div class="item-separator">&nbsp;</div>'; }

    /**
     * @see $section, $sectionLink, $category, $categoryLink
     * @access public
     */
    function categories()
    {
        ob_start();
        if ($this->showSection || $this->showCategory) {
          echo ($this->showCategory ? JText::_('Category') : JText::_('Section')) . ': ';
          if ($this->showSection) {
            echo '<span class="art-post-metadata-category-parent">';
            if (strlen($this->sectionLink))
              echo '<a href="' . $this->sectionLink . '">' . $this->_component->escape($this->section) . '</a>';
            else
              echo $this->_component->escape($this->section);
            echo '</span>';
            if ($this->showCategory)
              echo ' / ';
          }
          if ($this->showCategory) {
            echo '<span class="art-post-metadata-category-name">';
            if (strlen($this->categoryLink))
              echo '<a href="' . $this->categoryLink . '">' . $this->_component->escape($this->category) . '</a>';
            else
              echo $this->_component->escape($this->category);
            echo '</span>';
          }
        }        
        return ob_get_clean();
    }

    /**
     * @access public
     */
    function urlInfo()
    {
        return '<a href="http://' . $view->_article->urls . '" target="_blank">' . $view->_article->urls . '</a>';
    }

    /**
     * @access public
     */
    function event($name)
    {
        return $this->_article->event->{$name};
    }

    /**
     * @access public
     */
    function content()
    {
        return "<div class=\"art-article\">" . $this->_article->text . "</div>";
    }

    /**
     * @access public
     */
    function getArticleViewParameters()
    {
        return array('metadata-header-icons' => array(), 'metadata-footer-icons' => array());
    }

    /**
     * @access public
     */
    function article($article)
    {
        return artxPost($article);
    }
}
