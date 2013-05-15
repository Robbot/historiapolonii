<?php
defined('_JEXEC') or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$text = $params->get('text');

echo '<a href="' . $link . '" class="art-rss-tag-icon syndicate-module' . $moduleclass_sfx . '">'
  . ($text ? '<span>' . $text . '</span>' : '') . '</a>';
