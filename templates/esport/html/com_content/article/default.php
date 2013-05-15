<?php
defined('_JEXEC') or die;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'functions.php';

ArtxLoadClass("Artx_Content");

$component = new ArtxContent($this);
$article = $component->article('article');

echo $component->beginPageContainer('item-page');
if ($article->showPageHeading)
    echo $component->pageHeading($article->pageHeading);
$params = $article->getArticleViewParameters();
if ($article->titleVisible) {
    $params['header-text'] = $this->escape($article->title);
    if (strlen($article->titleLink))
        $params['header-link'] = $article->titleLink;
}
// Change the order of ""if"" statements to change the order of article metadata header items.
if ($article->showCreateDate)
    $params['metadata-header-icons'][] = "<span class=\"art-postdateicon\">" . $article->createDateInfo() . "</span>";
if ($article->showModifyDate)
    $params['metadata-header-icons'][] = "<span class=\"art-postdateicon\">" . $article->modifyDateInfo() . "</span>";
if ($article->showAuthor)
    $params['metadata-header-icons'][] = "<span class=\"art-postauthoricon\">" . $article->authorInfo() . "</span>";
if ($article->showPdfIcon)
    $params['metadata-header-icons'][] = $article->pdfIcon();
if ($article->showPrintIcon)
    $params['metadata-header-icons'][] = $article->printIcon();
if ($article->showEmailIcon)
    $params['metadata-header-icons'][] = $article->emailIcon();
if ($article->showEditIcon)
    $params['metadata-header-icons'][] = $article->editIcon();
if ($article->showUrl)
    $params['metadata-header-icons'][] = $article->urlInfo();
// Build article content
$content = '';
if (!$article->showIntro)
    $content .= $article->event('afterDisplayTitle');
$content .= $article->event('beforeDisplayContent');
$content .= $article->toc();
$content .= $article->content();
$content .= $article->event('afterDisplayContent');
$params['content'] = $content;
// Change the order of ""if"" statements to change the order of article metadata footer items.
if ($article->showSection || $article->showCategory)
    $params['metadata-footer-icons'][] = "<span class=\"art-postcategoryicon\">" . $article->categories() . "</span>";

// Render article
echo $article->article($params);
echo $component->endPageContainer();
