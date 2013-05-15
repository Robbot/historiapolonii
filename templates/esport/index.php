<?php
defined('_JEXEC') or die;

/**
 * Template for Joomla! CMS, created with Artisteer.
 * See readme.txt for more details on how to use the template.
 */

// Check if the template is compatible with the currently used Joomla version:
$version = new JVersion();
if ('1.5' != $version->RELEASE)
    exit('This template is not compatible with Joomla ' . $version->RELEASE . ' and should be replaced.');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';

// Create alias for $this object reference:
$document = & $this;

// Shortcut for the template base url:
$templateUrl = $document->baseurl . '/templates/' . $document->template;

ArtxLoadClass("Artx_Page");

// Initialize $view:
$view = $this->artx = new ArtxPage($this);

// Decorate component with Artisteer style:
$view->componentWrapper();

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?php echo $document->language; ?>">
<head>
    <jdoc:include type="head" />
    <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/system.css" />
    <link rel="stylesheet" href="<?php echo $document->baseurl; ?>/templates/system/css/general.css" />

    <!-- Created by Artisteer v4.1.0.59688 -->
    
    
    <meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width">

    <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.css" media="screen">
    <!--[if lte IE 7]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie7.css" media="screen" /><![endif]-->
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.responsive.css" media="all">


    <script>if ('undefined' != typeof jQuery) document._artxJQueryBackup = jQuery;</script>
    <script src="<?php echo $templateUrl; ?>/jquery.js"></script>
    <script>jQuery.noConflict();</script>

    <script src="<?php echo $templateUrl; ?>/script.js"></script>
    <?php $view->includeInlineScripts() ?>
    <script>if (document._artxJQueryBackup) jQuery = document._artxJQueryBackup;</script>
    <script src="<?php echo $templateUrl; ?>/script.responsive.js"></script>
</head>
<body>

<div id="art-main">
<header class="art-header clearfix"><?php echo $view->position('header', 'art-nostyle'); ?>


    <div class="art-shapes">
<h1 class="art-headline" data-left="4.69%">
    <a href="<?php echo $document->baseurl; ?>/"><?php echo $this->params->get('siteTitle'); ?></a>
</h1>
<h2 class="art-slogan" data-left="37.64%"><?php echo $this->params->get('siteSlogan'); ?></h2>


<div class="art-textblock art-object2067454004" data-left="0%">
        <div class="art-object2067454004-text"></div>
    
</div><div class="art-textblock art-object1222827553" data-left="1.65%">
        <div class="art-object1222827553-text"></div>
    
</div>
            </div>

<?php if ($view->containsModules('user3', 'extra1', 'extra2')) : ?>
<nav class="art-nav clearfix">
    <div class="art-nav-inner">
    
<?php if ($view->containsModules('extra1')) : ?>
<div class="art-hmenu-extra1"><?php echo $view->position('extra1'); ?></div>
<?php endif; ?>
<?php if ($view->containsModules('extra2')) : ?>
<div class="art-hmenu-extra2"><?php echo $view->position('extra2'); ?></div>
<?php endif; ?>
<?php echo $view->position('user3'); ?>
 
        </div>
    </nav>
<?php endif; ?>

                    
</header>
<div class="art-sheet clearfix">
            <?php echo $view->position('banner1', 'art-nostyle'); ?>
<?php echo $view->positions(array('top1' => 33, 'top2' => 33, 'top3' => 34), 'art-block'); ?>
<div class="art-layout-wrapper clearfix">
                <div class="art-content-layout">
                    <div class="art-content-layout-row">
                        <?php if ($view->containsModules('left')) : ?>
<div class="art-layout-cell art-sidebar1 clearfix">
<?php echo $view->position('left', 'art-block'); ?>



                        </div>
<?php endif; ?>
                        <div class="art-layout-cell art-content clearfix">
<?php
  echo $view->position('banner2', 'art-nostyle');
  if ($view->containsModules('breadcrumb'))
    echo artxPost($view->position('breadcrumb'));
  echo $view->positions(array('user1' => 50, 'user2' => 50), 'art-article');
  echo $view->position('banner3', 'art-nostyle');
  echo artxPost(array('content' => '<jdoc:include type="message" />', 'classes' => ' art-messages'));
  echo '<jdoc:include type="component" />';
  echo $view->position('banner4', 'art-nostyle');
  echo $view->positions(array('user4' => 50, 'user5' => 50), 'art-article');
  echo $view->position('banner5', 'art-nostyle');
?>



                        </div>
                    </div>
                </div>
            </div>
<?php echo $view->positions(array('bottom1' => 33, 'bottom2' => 33, 'bottom3' => 34), 'art-block'); ?>
<?php echo $view->position('banner6', 'art-nostyle'); ?>


    </div>
<footer class="art-footer clearfix">
  <div class="art-footer-inner">
<div class="art-content-layout">
    <div class="art-content-layout-row">
    <div class="art-layout-cell layout-item-0" style="width: 50%">
<?php if ($view->containsModules('footer1')) : ?>
    <?php echo $view->position('footer1', 'art-nostyle'); ?>
<?php else: ?>
        <p style="text-align: center;"><a href="#">Gift Cards</a>|<a href="#">Jobs</a>|<a href="#">Contacts Us</a>|<a href="#">Privacy Policy</a></p>
    <?php endif; ?>
</div><div class="art-layout-cell layout-item-0" style="width: 50%">
<?php if ($view->containsModules('footer2')) : ?>
    <?php echo $view->position('footer2', 'art-nostyle'); ?>
<?php else: ?>
        <p>Enim id fringilla libero quam ligula magna. <a href="http://www.iconfinder.com/search/?q=iconset:WPZOOM_Social_Networking_Icon_Set">Ikony</a> A. <a href="http://www.wpzoom.com/">David Ferreira</a></p>
    <?php endif; ?>
</div>
    </div>
</div>

    <p class="art-page-footer">
        <span id="art-footnote-links"><a href="http://www.artisteer.com/?p=joomla_templates" target="_blank">Joomla template</a> created with Artisteer.</span>
    </p>
  </div>
</footer>

</div>



<?php echo $view->position('debug'); ?>
</body>
</html>