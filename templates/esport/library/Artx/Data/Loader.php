<?php

ArtxLoadClass('Artx_Data_Mappers');

class Artx_Data_Loader
{
    /**
     * Loaded content.
     */
    var $_data;

    /**
     * The data item that is currently being loaded: article, module, etc.
     */
    var $_dataItem;

    /**
     * Path to the data item that is currently being loaded.
     */
    var $_path;

    /**
     * Absolute path to the directory with content images.
     */
    var $_images;

    /**
     * Name of the template.
     */
    var $_template;

    function load($file)
    {
        $path = realpath($file);
        if (false === $path)
            return;
        $images = dirname($path) . DIRECTORY_SEPARATOR . 'images';
        if (file_exists($images) && is_dir($images))
            $this->_images = $images;
        $this->_template = basename(dirname(dirname($path)));
        return $this->_parse($file);
    }

    function execute($params)
    {
        $callback = array();
        $callback[] = & $this;
        $callback[] = '_error';
        Artx_Data_Mappers::errorCallback($callback);

        $action = isset($params['action']) && is_string($params['action']) ? $params['action'] : '';
        if (0 == strlen($action) || !in_array($action, array('check', 'run', 'params')))
            return 'Invalid action.';
        switch ($action) {
            case 'check':
                echo 'result:' . ($this->_isInstalled() ? '1' : '0');
                break;
            case 'run':
                $this->_load();
                echo 'result:ok';
                break;
            case 'params':
                $parameters = array();
                foreach ($this->_data['parameter'] as $key => $parameterData){
                    $parameters['jform_params_' . $parameterData['name']] = $parameterData['value'];
                }
                echo 'params:' . json_encode($parameters);
                break;
        }
    }

    function _error($msg, $code)
    {
        exit('error:' . $code . ':' . $msg);
    }

    function _isInstalled()
    {                          
        $sections = & Artx_Data_Mappers::get('section');
        $menus = & Artx_Data_Mappers::get('menu');
        $modules = & Artx_Data_Mappers::get('module');
        foreach ($this->_data['section'] as $value) {
            $sectionsList = & $sections->find(array('scope' => 'content', 'title' => $value['title']));
            if (0 != count($sectionsList))
                return true;
        }

        foreach ($this->_data['menu'] as $value) {
            $menusList = & $menus->find(array('title' => $value['title']));
            if (0 != count($menusList))
                return true;
        }
        
        foreach ($this->_data['module'] as $value) {
            $modulesList = & $modules->find(array('title' => $value['title']));
            if (0 != count($modulesList))
                return true;
        }

        return false;
    }

    function _load()
    {
        $this->_cleanup();
        $this->_loadContent();
        $this->_loadMenus();
        $this->_createModules();
        $this->_updateContent();
        $this->_configureModulesVisibility();
        $this->_setParameters();
        $this->_configureEditor();
        $this->_copyImages();
    }

    function _cleanup()
    {           
        $sections = & Artx_Data_Mappers::get('section');
        $menus = & Artx_Data_Mappers::get('menu');
        $modules = & Artx_Data_Mappers::get('module');

        foreach ($this->_data['section'] as $sectionData) {
            $sectionList = & $sections->find(array('scope' => 'content', 'title' => $sectionData['title']));
            foreach ($sectionList as $sectionListItem)
                $sections->delete($sectionListItem->id);
        }

        foreach ($this->_data['menu'] as $menuData) {
            $menuList = & $menus->find(array('title' => $menuData['title']));
            foreach ($menuList as $menuListItem)
                $menus->delete($menuListItem->id);
        }

        foreach ($this->_data['module'] as $moduleData) {
            $moduleList = & $modules->find(array('title' => $moduleData['title']));
            foreach ($moduleList as $moduleListItem)
                $modules->delete($moduleListItem->id);
        }
    }

    function _loadContent()
    {
        $sections = & Artx_Data_Mappers::get('section');
        $categories = & Artx_Data_Mappers::get('category');
        $content = & Artx_Data_Mappers::get('content');

        foreach ($this->_data['section'] as & $sectionData) {
            $section = & $sections->create();
            $section->title = $sectionData['title'];
            $section->scope = 'content';
            $status = $sections->save($section);
            if (is_string($status))
                return $this->_error($status, 1);
            $sectionData['joomla_id'] = $section->id;
        }

        foreach ($this->_data['category'] as & $categoryData) {
            $category = & $categories->create();
            $category->title = $categoryData['title'];
            $category->section = $this->_data['section'][$categoryData['section']]['joomla_id'];
            $status = $categories->save($category);
            if (is_string($status))
                return $this->_error($status, 1);
            $categoryData['joomla_id'] = $category->id;
        }

        foreach ($this->_data['article'] as & $articleData) {
            $article = & $content->create();
            $article->sectionid = $this->_data['section'][$articleData['section']]['joomla_id'];
            $article->catid = $this->_data['category'][$articleData['category']]['joomla_id'];
            $article->title = $articleData['title'];
            $article->alias = $articleData['alias'];
            $article->introtext = $articleData['text'];
            $article->attribs = $this->_paramsToString(array
                (
                    'show_title' => '',
                    'link_titles' => '',
                    'show_intro' => '',
                    'show_section' => '',
                    'link_section' => '',
                    'show_category' => '',
                    'link_category' => '',
                    'show_vote' => '',
                    'show_author' => '',
                    'show_create_date' => '',
                    'show_modify_date' => '',
                    'show_pdf_icon' => '',
                    'show_print_icon' => '',
                    'show_email_icon' => '',
                    'language' => '',
                    'keyref' => '',
                    'readmore' => ''
                ));
            $article->metadata = $this->_paramsToString(array('robots' => '', 'author' => ''));
            $status = $content->save($article);
            if (is_string($status))
                return $this->_error($status, 1);
            $articleData['joomla_id'] = $article->id;
        }
    }

    function _loadMenus()
    {
        $menus = & Artx_Data_Mappers::get('menu');
        $menuItems = & Artx_Data_Mappers::get('menuItem');

        $order = array();

        foreach ($this->_data['menu'] as $menuData) {
            $menu = & $menus->create();
            $menu->menutype = $menuData['name'];
            $menu->title = $menuData['title'];
            $status = $menus->save($menu);
            if (is_string($status))
                return $this->_error($status, 1);
            $order[$menuData['name']] = 1;
        }

        foreach ($this->_data['menuitem'] as & $itemData) {
            $item = & $menuItems->create();

            // change the default menu item:
            if (isset($itemData['default']) && $itemData['default']) {
                // * clean up the home flag of all menu items:
                $homeItems = & $menuItems->find(array('home' => 1));
                foreach ($homeItems as $key => $homeItem) {
                    $homeItems[$key]->home = '0';
                    $menuItems->save($homeItems[$key]);
                }
                // * set up the home flag for the current item:
                $item->home = '1';
            }

            $item->name = $itemData['title'];
            $item->menutype = $itemData['menu'];
            $item->alias = $itemData['alias'];

            $params = array();
            switch ($itemData['type']) {
                case 'single-article':
                    $id = '';
                    if (isset($itemData['article']))
                        $id = $this->_data['article'][$itemData['article']]['joomla_id'];
                    $item->link = 'index.php?option=com_content&view=article&id=' . $id;
                    $item->type = 'component';
                    $item->componentid = '20';
                    $params = array
                        (
                            'show_noauth' => '',
                            'show_title' => 'yes' === $itemData['showTitle'] ? '1' : '0',
                            'link_titles' => '',
                            'show_intro' => '',
                            'show_section' => '0',
                            'link_section' => '',
                            'show_category' => '0',
                            'link_category' => '',
                            'show_author' => '0',
                            'show_create_date' => '0',
                            'show_modify_date' => '0',
                            'show_item_navigation' => '0',
                            'show_readmore' => '',
                            'show_vote' => '0',
                            'show_icons' => '0',
                            'show_pdf_icon' => '0',
                            'show_print_icon' => '0',
                            'show_email_icon' => '0',
                            'show_hits' => '0',
                            'feed_summary' => '',
                            'page_title' => '',
                            'show_page_title' => '1',
                            'pageclass_sfx' => '',
                            'menu_image' => '-1',
                            'secure' => '0'
                        );
                    break;
                case 'category-blog-layout':
                    $item->link = 'index.php?option=com_content&view=category&layout=blog&id='
                        . $this->_data['category'][$itemData['category']]['joomla_id'];
                    $item->type = 'component';
                    $item->componentid = '20';
                    $params = array
                        (
                            'show_description' => '0',
                            'show_description_image' => '0',
                            'num_leading_articles' => '0',
                            'num_intro_articles' => '4',
                            'num_columns' => '1',
                            'num_links' => '4',
                            'orderby_pri' => '',
                            'orderby_sec' => 'order',
                            'multi_column_order' => '0',
                            'show_pagination' => '2',
                            'show_pagination_results' => '1',
                            'show_feed_link' => '1',
                            'show_noauth' => '',
                            'show_title' => '',
                            'link_titles' => '',
                            'show_intro' => '',
                            'show_section' => '',
                            'link_section' => '',
                            'show_category' => '',
                            'link_category' => '',
                            'show_author' => '',
                            'show_create_date' => '',
                            'show_modify_date' => '',
                            'show_item_navigation' => '0',
                            'show_readmore' => '',
                            'show_vote' => '',
                            'show_icons' => '',
                            'show_pdf_icon' => '',
                            'show_print_icon' => '',
                            'show_email_icon' => '',
                            'show_hits' => '',
                            'feed_summary' => '',
                            'page_title' => '',
                            'show_page_title' => '0',
                            'pageclass_sfx' => '',
                            'menu_image' => '-1',
                            'secure' => '0'
                        );
                    break;
            }

            // parameters:
            $item->params = $this->_paramsToString($params);

            // ordering:
            $item->ordering = $order[$itemData['menu']];
            $order[$itemData['menu']]++;

            // parent and leveling:
            if (isset($itemData['parent'])) {
                $parent = & $this->_data['menuitem'][$itemData['parent']];
                $item->parent = $parent['joomla_id'];
                $itemData['sublevel'] = $parent['sublevel'] + 1;
            } else
                $itemData['sublevel'] = 0;
            $item->sublevel = $itemData['sublevel'];

            $status = $menuItems->save($item);
            if (is_string($status))
                $this->_error($status, 1);
            $itemData['joomla_id'] = $item->id;
        }
    }

    function _updateContent()
    {
        $content = & Artx_Data_Mappers::get('content');
        foreach ($this->_data['article'] as $key => $articleData) {
            $article = $content->fetch($articleData['joomla_id']);
            if (!is_null($article)) {
                $text = $this->_processingContent($articleData['text']);
                $parts = explode('<!--CUT-->', $text);
                $article->introtext = $parts[0];
                if (count($parts) > 1)
                    $article->fulltext = $parts[1];
                $status = $content->save($article);
                if (is_string($status))
                    return $this->_error($status, 1);
            }
        }
    }
    
    function _createModules()
    {
        $modules = & Artx_Data_Mappers::get('module');

        $order = array();

        foreach ($this->_data['module'] as & $moduleData) {
            $module = & $modules->create();
            $module->title = $moduleData['title'];
            $module->position = $moduleData['position'];

            $params = array();
            switch ($moduleData['type']) {
                case 'menu':
                    $module->module = 'mod_mainmenu';
                    $params = array
                        (
                            'menutype' => $moduleData['menu'],
                            'menu_style' => 'list',
                            'startLevel' => '0',
                            'endLevel' => '0',
                            'showAllChildren' => '1',
                            'window_open' => '',
                            'show_whitespace' => '0',
                            'cache' => '1',
                            'tag_id' => '',
                            'class_sfx' => '',
                            'moduleclass_sfx' => '',
                            'maxdepth' => '10',
                            'menu_images' => '0',
                            'menu_images_align' => '0',
                            'menu_images_link' => '0',
                            'expand_menu' => '0',
                            'activate_parent' => '0',
                            'full_active_id' => '0',
                            'indent_image' => '0',
                            'indent_image1' => '',
                            'indent_image2' => '',
                            'indent_image3' => '',
                            'indent_image4' => '',
                            'indent_image5' => '',
                            'indent_image6' => '',
                            'spacer' => '',
                            'end_spacer' => ''
                        );
                    break;
                case 'login':
                    $module->module = 'mod_login';
                    $params = array
                        (
                            'cache' => '0',
                            'moduleclass_sfx' => '',
                            'pretext' => '',
                            'posttext' => '',
                            'login' => '',
                            'logout' => '',
                            'greeting' => '1',
                            'name' => '0',
                            'usesecure' => '0'
                        );
                    break;
                case 'custom':
                    $module->module = 'mod_custom';
                    $module->content = $this->_processingContent($moduleData['content']);
                    $params = array('moduleclass_sfx' => '');
                    break;
            }

            // show title:
            $module->showtitle = 'true' == $moduleData['showTitle'] ? '1' : '0';

            // style:
            if (isset($moduleData['style']) && isset($params['moduleclass_sfx']))
                $params['moduleclass_sfx'] = $moduleData['style'];
                
            // parameters:
            $module->params = $this->_paramsToString($params);

            // ordering:
            if (!isset($order[$moduleData['position']]))
                $order[$moduleData['position']] = 1;
            $module->ordering = $order[$moduleData['position']];
            $order[$moduleData['position']]++;

            $status = $modules->save($module);
            if (is_string($status))
                return $this->_error($status, 1);
            $moduleData['joomla_id'] = $module->id;
        }
    }

    function _parseHref($matches)
    {
        $path = urldecode($matches[1]);
        $menuItems = & Artx_Data_Mappers::get('menuItem');
        foreach ($this->_data['menuitem'] as $key => $itemData) {
            if (isset($itemData['path']) && $path === $itemData['path']) {
                $menuItem = $menuItems->fetch($itemData['joomla_id']);
                if (!is_null($menuItem))
                    return 'href="' . $menuItem->link . '&Itemid=' . $menuItem->id . '"';
            }
        }
        
        $content = & Artx_Data_Mappers::get('content');
        $specialMenuItems = array_slice($this->_data['menuitem'], -2);
        foreach ($this->_data['article'] as $key => $articleData) {
            if (isset($articleData['path']) && $path === $articleData['path']) {
                $article = $content->fetch($articleData['joomla_id']);
                $itemId = strstr($path, '/Blog Posts/') ? $specialMenuItems[0] : $specialMenuItems[1];
                if (!is_null($article))
                    return 'href="index.php?option=com_content&amp;view=article'.
                    '&amp;id=' . $article->id . '&amp;catid=' . $article->catid . 
                    '&amp;Itemid=' . $itemId['joomla_id'] . '"';
            }
        }
        return $matches[0];
    }

    function _processingContent($content){
        
        $config = JFactory::getConfig();
        $live_site = $config->get('live_site');
        $root = trim($live_site) != '' ? JURI::root(true) : dirname(dirname(dirname(JURI::root(true))));
        if ('/' === substr($root, -1)) 
            $root  = substr($root, 0, -1);

        $content = preg_replace('/url\(\'images\//', 'url(\'' . $root .'/images/', $content);
        $content = preg_replace('/src="images\/template\//', 'src="' . $root .'/templates/' . $this->_template . '/images/', $content);
        $content = preg_replace_callback('/href="?([^"]*)"/', array( &$this, '_parseHref'), $content);
        return $content;
    }

    function _configureModulesVisibility()
    {
        $contentMenuItems = array();
        foreach ($this->_data['menuitem'] as $item)
            $contentMenuItems[] = $item['joomla_id'];

        $contentModules = array();
        foreach ($this->_data['module'] as $module)
            $contentModules[] = $module['joomla_id'];

        $modules = & Artx_Data_Mappers::get('module');
        $menuItems = & Artx_Data_Mappers::get('menuItem');

        $userMenuItems = array();
        $menuItemList = & $menuItems->find();
        foreach ($menuItemList as $menuItem) {
            if (in_array($menuItem->id, $contentMenuItems))
                continue;
            $userMenuItems[] = $menuItem->id;
        }

        $moduleList = & $modules->find(array('scope' => 'site'));
        foreach ($moduleList as $moduleListItem) {
            if (in_array($moduleListItem->id, $contentModules)) {
                $modules->enableOn($moduleListItem->id, $contentMenuItems);
            } else {
                $pages = $modules->enabledOn($moduleListItem->id);
                if (1 == count($pages) && '0' == $pages[0])
                    $modules->enableOn($moduleListItem->id, $userMenuItems);
            }
        }
    }

    function _setParameters() {
        $path = dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'params.ini';
        if (false === $path) {
            return $this->_error('params.ini is not accessible', 1);
        }
        if (!file_exists($path)) {
            return $this->_error('params.ini does not exist', 1);
        }
        if (!is_writable($path)) {
            return $this->_error('params.ini is not writable', 1);
        }

        $content = file_get_contents($path);
        $parameters = $this->_stringToParams($content);

        foreach ($this->_data['parameter'] as $parameterData)
            $parameters[$parameterData['name']] = $parameterData['value'];

        file_put_contents($path, $this->_paramsToString($parameters));
    }

    function _configureEditor()
    {
        $plugins = & Artx_Data_Mappers::get('plugin');
        $tinyMce = & $plugins->findOne(array('element' => 'tinymce'));
        if (is_string($tinyMce))
            return $this->_error($tinyMce, 1);
        if (!is_null($tinyMce)) {
            $params = $this->_stringToParams($tinyMce->params);
            $elements = strlen($params['extended_elements']) ? explode(',', $params['extended_elements']) : array();
            if (!in_array('style', $elements))
                $elements[] = 'style';
            if (!in_array('script', $elements))
                $elements[] = 'script';
            $params['extended_elements'] = implode(',', $elements);
            $tinyMce->params = $this->_paramsToString($params);
            $status = $plugins->save($tinyMce);
            if (is_string($status))
                return $this->_error($status, 1);
        }
        return null;
    }

    function _copyImages()
    {
        if (is_null($this->_images) || 0 == strlen($this->_images))
            return;
        $imgDir = dirname(JPATH_BASE) . DS . 'images';
        $contentDir = $imgDir . DS . 'template-content';
        if (!file_exists($contentDir))
            mkdir($contentDir);
        if ($handle = opendir($this->_images)) {
            while (false !== ($file = readdir($handle))) {
                if ('.' == $file || '..' == $file || is_dir($file))
                    continue;
                if (!preg_match('~\.(?:bmp|jpg|jpeg|png|ico|gif)$~i', $file))
                    continue;
                copy($this->_images . DS . $file, $contentDir . DS . $file);
            }
            closedir($handle);
        }
    }

    function _paramsToString($params)
    {
        $registry = new JRegistry();
        $registry->loadArray($params);
        return $registry->toString();
    }

    function _stringToParams($string)
    {
        $registry = new JRegistry();
        $registry->loadINI($string);
        return $registry->toArray();
    }

    /**
     * Loads the content of the XML file specified by the $file parameter to the $_data class field.
     */
    function _parse($file)
    {
        $this->_data = array
            (
                'section' => array(),
                'category' => array(),
                'article' => array(),
                'menu' => array(),
                'menuitem' => array(),
                'module' => array(),
                'parameter' => array()
            );
        $this->_dataItem = null;

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);      
        xml_set_object($parser, $this);                          
        xml_set_element_handler($parser, '_parserStartElementHandler', '_parserEndElementHandler'); 
        xml_set_character_data_handler($parser, '_parserCharacterDataHandler');

        $error = null;
        if (!($fp = fopen($file, "r")))
            $error = 'Could not open XML input';
        if (is_null($error)) {
            while ($data = fread($fp, 4096)) {
                if (xml_parse($parser, $data, feof($fp)))
                    continue;
                $error = 'XML error: ' . xml_error_string(xml_get_error_code($parser))
                    . ' at line ' . xml_get_current_line_number($parser);
                break;
            }
        }
        xml_parser_free($parser);
        
        // Clean up the _dataItem reference:
        $null = null;
        $this->_dataItem = & $null;

        // Initialize _path
        $this->_path = array();

        return $error;
    }

    function _parserStartElementHandler($parser, $name, $attrs)
    {
        $this->_path[] = $name;
        $path = implode('/', $this->_path);
        switch ($path) {
            case 'data/sections/section':
            case 'data/categories/category':
            case 'data/articles/article':
            case 'data/menus/menu':
            case 'data/menuitems/menuitem':
            case 'data/modules/module':
            case 'data/parameters/parameter':
                $this->_data[$name][$attrs['id']] = $attrs;
                $this->_dataItem = & $this->_data[$name][$attrs['id']];
                $this->_dataItem['entity'] = $name;
                break;
            case 'data/categories/category/parameters/parameter':
                $this->_dataItem['parameters'][$attrs['name']] = $attrs['value'];
                break;
        }
    }

    function _parserEndElementHandler($parser, $name)
    {
        array_pop($this->_path);
    }

    function _parserCharacterDataHandler($parser, $data)
    {
        switch ($this->_dataItem['entity']) {
            case 'article':
                if (!isset($this->_dataItem['text']))
                    $this->_dataItem['text'] = '';
                $this->_dataItem['text'] .= $data;
                break;
            case 'module':
                if (!isset($this->_dataItem['content']))
                    $this->_dataItem['content'] = '';
                $this->_dataItem['content'] .= $data;
                break;
        }
    }
}
