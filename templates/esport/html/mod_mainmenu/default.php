<?php
defined('_JEXEC') or die;

ob_start();
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DS
    . 'modules' . DS . 'mod_mainmenu' . DS . 'tmpl' . DS . 'default.php';
ob_clean();

if (!defined('modMainMenuArtxExtensions'))
{

	function modMainMenuArtXMLCallback(&$node, $args)
	{
		$options = $GLOBALS['modMainMenuArtXMLCallbackOptions'];
		if ($node->name() == 'li') {
			if (!$options['show_submenus'] && $node->level() == 1) {
				if ($ul = $node->getElementByPath('ul'))
					$node->removeChild($ul);
			}
			$liChildren = & $node->_children;
			if (count($liChildren) > 0) {
				// element                 ( $img?,                                          $span ( $text     )?  )      )
				// <a href="...">          <img src="..." (align="left|right")? alt="..." />?<span><![CDATA[...]]></span>?</a>
				// <span class="separator"><img src="..." (align="left|right")? alt="..." />?<span><![CDATA[...]]></span>?</span>
				$element = & $liChildren[0];
				$img = null;
				if ($element->_children[0]->name() == 'img') {
					$img = & $element->_children[0];
					$element->removeChild($img);
				}
				if (count($element->_children) > 0) {
					$span = & $element->_children[0];
					$text = $span->data();
					$element->removeChild($span);
				} else {
					$span = null;
					$text = null;
				}
				if (count($liChildren) > 1)
					$sublist = & $liChildren[1];
				else
					$sublist = null;
				// convert separator to anchor
				if ($element->name() == 'span' && $element->attributes('class') == 'separator') {
					$element->_name = 'a';
					$element->addAttribute('href', '#');
					$element->addAttribute('onclick', 'return false;');
					if ($sublist == null)
						$element->addAttribute('class', 'separator-without-submenu');
				}
				if ($img != null) {
					$align = $img->attributes('align');
					if (null == $text) {
						$element->addChild('img', array('class' => 'art-menu-image-left',
							'src' => $img->attributes('src'),
							'alt' => $img->attributes('alt')));
					} else if ($align == 'left' || $align == '') {
						$element->addChild('img', array('class' => 'art-menu-image-left',
							'src' => $img->attributes('src'),
							'alt' => $img->attributes('alt')));
						$extraArtxSpan = & $element->addChild('artx-extra-span');
						$extraArtxSpan->setData($text);
					} else if ($align == 'right') {
						$extraArtxSpan = & $element->addChild('artx-extra-span');
						$extraArtxSpan->setData($text);
						$element->addChild('img', array('class' => 'art-menu-image-right',
							'src' => $img->attributes('src'),
							'alt' => $img->attributes('alt')));
					}
				} else if (null != $text) {
					$element->setData($text);
				}
			}

			modMainMenuXMLCallback($node, $args);

			// Add active class to ul tags with active elements.
			// It should be located after modMainMenuXMLCallback, because the callback sets class='active' and id='current'.
			if (null != $options['vmenu']) {
				$class = $node->attributes('class');
				$active = $class && false !== strpos(' ' . $class, ' active');
				if ($active) {
					$ul = null;
					foreach (array_keys($node->_children) as $key)
						if ($node->_children[$key]->name() == 'ul') {
							$ul = & $node->_children[$key];
							break;
						}
					if ($ul)
						$ul->addAttribute('class', 'active');
				}
			}

			$class = $node->attributes('class');
			if ($class && false !== strpos(' ' . $class, ' active'))
				$element->addAttribute('class', $element->attributes('class') . ' active');
		} else {
			modMainMenuXMLCallback($node, $args);
		}
	}

	define('modMainMenuArtxExtensions', true);
}

if (isset($attribs['name']) && $attribs['name'] == 'user3') {
	$GLOBALS['modMainMenuArtXMLCallbackOptions'] = array(
		'show_submenus' => $GLOBALS['artx_settings']['menu']['show_submenus'] && 1 == $params->get('showAllChildren'),
		'vmenu' => null,
		'start' => $params->get('startLevel')
	);
	$xml = modMainMenuHelper::getXML($params->get('menutype'), $params, 'modMainMenuArtXMLCallback');
	if ($xml) {
		$xml->addAttribute('class', 'art-hmenu');
		if ($tagId = $params->get('tag_id')) {
			$xml->addAttribute('id', $tagId);
		}
		$result = JFilterOutput::ampReplace($xml->toString((bool)$params->get('show_whitespace')));
		$result = str_replace(array('<ul/>', '<ul />', '<artx-extra-span>', '</artx-extra-span>'), '', $result);
		$result = preg_replace('~<span([^>]*) />~', '<span$1></span>', $result);
		echo $result;
	}
	unset($GLOBALS['tmp_menu_show_submenus']);
} else if (0 === strpos($params->get('moduleclass_sfx'), 'art-vmenu') || false !== strpos($params->get('moduleclass_sfx'), ' art-vmenu')) {
	$GLOBALS['modMainMenuArtXMLCallbackOptions'] = array(
		'show_submenus' => $GLOBALS['artx_settings']['vmenu']['show_submenus'] && 1 == $params->get('showAllChildren'),
		'vmenu' => array('simple' => $GLOBALS['artx_settings']['vmenu']['simple']),
		'start' => $params->get('startLevel')
	);
	$xml = modMainMenuHelper::getXML($params->get('menutype'), $params, 'modMainMenuArtXMLCallback');
	if ($xml) {
		$xml->addAttribute('class', 'art-vmenu');
		if ($tagId = $params->get('tag_id')) {
			$xml->addAttribute('id', $tagId);
		}
		$result = JFilterOutput::ampReplace($xml->toString((bool)$params->get('show_whitespace')));
		$result = str_replace(array('<ul/>', '<ul />', '<artx-extra-span>', '</artx-extra-span>'), '', $result);
		$result = preg_replace('~<span([^>]*) />~', '<span$1></span>', $result);
		echo $result;
	}
	unset($GLOBALS['tmp_menu_show_submenus']);
} else {
	modMainMenuHelper::render($params, 'modMainMenuXMLCallback');
}