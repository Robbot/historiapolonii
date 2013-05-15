<?php
defined('_JEXEC') or die;

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'functions.php';

ArtxLoadClass("Artx_Content");

$view = new ArtxContent($this);

echo artxPost(array('header-text' => $view->pageHeading));

$cparams =& JComponentHelper::getParams('com_media');

if (($this->params->get('show_description_image') && $this->section->image)
    || ($this->params->get('show_description') && $this->section->description))
{
    ob_start();
    if ($this->params->get('show_description_image') && $this->section->image)
        echo '<img src="' . $this->baseurl . '/' . $cparams->get('image_path') . '/' . $this->section->image
            . '" align="' . $this->section->image_position . '" hspace="6" alt="" />';
    if ($this->params->get('show_description') && $this->section->description)
        echo $this->section->description;
    echo artxPost(ob_get_clean());
}

if ($this->params->def('num_leading_articles', 1)) : ?>
<table class="blog<?php echo $view->pageClassSfx; ?>" cellpadding="0" cellspacing="0" width="100%">
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
</table>
<?php else : ?>
<?php   $i = $this->pagination->limitstart; ?>
<?php endif; ?>
<?php
$startIntroArticles = $this->pagination->limitstart + $this->params->get('num_leading_articles');
$numIntroArticles = $startIntroArticles + $this->params->get('num_intro_articles', 4);
if (($numIntroArticles != $startIntroArticles) && ($i < $this->total)) : ?>
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
                        $i = $i + $this->params->get('num_intro_articles', 4) ; 
            else : // otherwise, order down, same as before (default behaviour)
                for ($z = 0; $z < $this->params->get('num_columns'); $z ++) :
                if ($z > 0) : $divider = " column_separator"; endif; ?>
                <td valign="top" width="<?php echo intval(100 / $this->params->get('num_columns')) ?>%" class="article_column<?php echo $divider ?>">
                <?php for ($y = 0; $y < ($this->params->get('num_intro_articles', 4) / $this->params->get('num_columns')); $y ++) :
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
<?php
endif;

if ($this->params->def('num_links', 4) && ($i < $this->total)) {
    $this->links = array_splice($this->items, $i - $this->pagination->limitstart);
    if (count($this->links) > 0) {
        ob_start();
        echo $this->loadTemplate('links');
        echo artxPost(ob_get_clean());
    }
}
if ($this->params->def('show_pagination', 2) == 1
    || ($this->params->get('show_pagination') == 2
        && $this->pagination->get('pages.total') > 1))
{
    ob_start();
    echo '<div id="navigation">';
    echo '<p>' . $this->pagination->getPagesLinks() . '</p>';
    if ($this->params->def('show_pagination_results', 1))
        echo '<p>' . $this->pagination->getPagesCounter() . '</p>';
    echo '</div>';
    echo ob_get_clean();
}
