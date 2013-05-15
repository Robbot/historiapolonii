<?php
defined('_JEXEC') or die;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'functions.php';

ArtxLoadClass("Artx_Content");

$view = new ArtxContent($this);

echo $view->beginPageContainer('blog');

ob_start();

if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) {
    echo '<div class="category-desc">';
    if ($this->params->get('show_description_image') && $this->category->image){
        $params = JComponentHelper::getParams('com_media');
        echo '<img src="' . $this->baseurl . '/' . $params->get('image_path')
            . '/' . $this->category->image . '" align="' . $this->category->image_position . '" hspace="6" alt="" />';
    }
    if ($this->params->get('show_description') && $this->category->description)
        echo $this->category->description;
    echo '</div>';
}
echo artxPost(array('header-text' => $view->pageHeading, 'content' => ob_get_clean()));

?>
<table class="blog<?php echo $view->pageClassSfx;?>" cellpadding="0" cellspacing="0" width="100%">
<?php if ($this->params->get('num_leading_articles')) : ?>
<tr>
    <td valign="top">
    <?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
        <?php if ($i >= $this->total) : break; endif; ?>
        <div>
        <?php
            $this->item =& $this->getItem($i, $this->params);
            echo $this->loadTemplate('item');
        ?>
        </div>
    <?php endfor; ?>
    </td>
</tr>
<?php else : $i = $this->pagination->limitstart; endif; ?>

<?php
$startIntroArticles = $this->pagination->limitstart + $this->params->get('num_leading_articles');
$numIntroArticles = $startIntroArticles + $this->params->get('num_intro_articles');
if (($numIntroArticles != $startIntroArticles) && ($i < $this->total)) : ?>
<tr>
    <td valign="top">
        <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
        <?php
            $divider = '';
            if ($this->params->get('multi_column_order')) : // order across, like front page
                for ($z = 0; $z < $this->params->def('num_columns', 2); $z ++) :
                    if ($z > 0) : $divider = " column_separator"; endif;
                    $rows = (int) ($this->params->get('num_intro_articles', 4) / $this->params->get('num_columns'));
                    $cols = ($this->params->get('num_intro_articles', 4) % $this->params->get('num_columns'));
                    ?>
                    <td valign="top"
                        width="<?php echo intval(100 / $this->params->get('num_columns')) ?>%"
                        class="article_column<?php echo $divider ?>">
                        <?php
                        $loop = (($z < $cols)?1:0) + $rows;

                        for ($y = 0; $y < $loop; $y ++) :
                            $target = $i + ($y * $this->params->get('num_columns')) + $z;
                            if ($target < $this->total && $target < ($numIntroArticles)) :
                                $this->item =& $this->getItem($target, $this->params);
                                echo $this->loadTemplate('item');
                            endif;
                        endfor;
                        ?></td>
                <?php endfor; 
                        $i = $i + $this->params->get('num_intro_articles') ; 
            else : // otherwise, order down, same as before (default behaviour)
                for ($z = 0; $z < $this->params->get('num_columns'); $z ++) :
                    if ($z > 0) : $divider = " column_separator"; endif; ?>
                    <td valign="top" width="<?php echo intval(100 / $this->params->get('num_columns')) ?>%" class="article_column<?php echo $divider ?>">
                    <?php for ($y = 0; $y < ($this->params->get('num_intro_articles') / $this->params->get('num_columns')); $y ++) :
                    if ($i < $this->total && $i < ($numIntroArticles)) :
                        $this->item =& $this->getItem($i, $this->params);
                        echo $this->loadTemplate('item');
                        $i ++;
                    endif;
                endfor; ?>
                </td>
        <?php endfor; 
        endif; ?> 
        </tr>
        </table>
    </td>
</tr>
<?php endif; ?>
</table>
<?php
if ($this->params->get('num_links') && ($i < $this->total)) {
    ob_start();
    $this->links = array_splice($this->items, $i - $this->pagination->limitstart);
    echo $this->loadTemplate('links');
    echo artxPost(ob_get_clean());
}

$paginationPagesLinks = $this->params->get('show_pagination') ? $this->pagination->getPagesLinks() : '';
$paginationPagesCounter = $this->params->get('show_pagination_results') ? $this->pagination->getPagesCounter() : '';
if (strlen($paginationPagesLinks) || strlen($paginationPagesCounter)) {
    ob_start();
    echo '<div id="navigation">';
    if (strlen($paginationPagesLinks) > 0)
        echo '<p>' . $paginationPagesLinks . '</p>';
    if (strlen($paginationPagesCounter) > 0)
        echo '<p>' . $paginationPagesCounter . '</p>';
    echo '</div>';
    echo ob_get_clean();
}

?>
<?php echo $view->endPageContainer(); ?>