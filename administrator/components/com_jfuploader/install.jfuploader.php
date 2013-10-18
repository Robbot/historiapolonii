<?php
/**
 * JFUploader 3.1 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2013 TinyWebGallery
 * written by Michael Dempfle
 *
 * @license GNU / GPL  
 *  
 * For the latest version please go to http://jfu.tinywebgallery.com
**/
// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );



// load the language file for the install
if (!defined( '_SELFUPDATE_' )) {
  $params   = JComponentHelper::getParams('com_languages');
  $backend_lang = $params->get('admin', 'en-GB');
  $language = JLanguage::getInstance($backend_lang);
  $language = JFactory::getLanguage();
  $language->load('com_jfuploader');
}

function com_install() { 
  $cur_version = '3.1';
  $database = JFactory::getDBO();
  
  
  $error=false;
  echo JText::_('I_CHECK');
  
  // we check if we have a previous version
  $database->setQuery("CREATE TABLE IF NOT EXISTS  #__joomla_flash_uploader_conf (key_id TEXT, value TEXT)");
  if(!$database->query()) echo $database->getErrorMsg().'<br />';
  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'version'");
  $version = $database->loadObjectList();
  
  //add new admin menu images
  $database->setQuery( "UPDATE #__components SET admin_menu_img = '../administrator/components/com_jfuploader/images/jfu_menu_icon.png' WHERE admin_menu_link = 'option=com_jfuploader'");
	$database->query();
	$database->setQuery( "UPDATE #__components SET admin_menu_img = '../administrator/components/com_jfuploader/images/jfu_menu_icon.png' WHERE admin_menu_link = 'option=com_jfuploader&act=upload'");
	$database->query();
	$database->setQuery( "UPDATE #__components SET admin_menu_img = 'js/ThemeOffice/config.png' WHERE admin_menu_link = 'option=com_jfuploader&act=config'");
	$database->query();
	$database->setQuery( "UPDATE #__components SET admin_menu_img = 'js/ThemeOffice/user.png' WHERE admin_menu_link = 'option=com_jfuploader&act=user'");
	$database->query();
	$database->setQuery( "UPDATE #__components SET admin_menu_img = 'js/ThemeOffice/info.png' WHERE admin_menu_link = 'option=com_jfuploader&act=help'");
	$database->query();
	$database->setQuery( "UPDATE #__components SET admin_menu_img = '../administrator/components/com_jfuploader/images/jfu_plugin_menu.png' WHERE admin_menu_link = 'option=com_jfuploader&act=plugins'");
	$database->query();
  	
  // end version check 
  if(!$version) { // tables do not exist
	echo  JText::_('I_CHECK_NONE');
    install_db($cur_version); // we install basic version.
    $error = update_db(true); // and then we update to current version.
 } else {
    $ver = $version[0]->value;
    if ($ver == '' || version_compare($ver,$cur_version, "<")) { 
      echo JText::_('I_CHECK_OLD_FOUND') . ' ' . $ver . JText::_('I_CHECK_OLD_FOUND_2');
      $error = update_db(false);
    } else {
      echo JText::_('I_CHECK_FOUND');
      $error = update_db(false);
    }
 }
 
  // remove after testing
  if (!testEntry('check_image_magic')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('check_image_magic','true')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	}  
  if (!$error) {
       // we update the version of the db table
	  $database->setQuery("UPDATE #__joomla_flash_uploader_conf SET value='".$cur_version."' WHERE key_id='version'");
	  if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
  
  
    $filename = dirname(__FILE__) . "/tfu/twg.lic.php";
    if (!file_exists($filename)) {
       // we try to the license now
       $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'user'");
       $user = $database->loadObjectList();
         if (count($user) > 0) {
             $us = $user[0]->value;
             $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'domain'");
             $domain = $database->loadObjectList();
             $do = $domain[0]->value;
             $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'code'");
             $code = $database->loadObjectList();
             $co = $code[0]->value;
             // write twg.lic.twg !
            
              $file = fopen($filename, 'w');
            	fputs($file, "<?php\n");
            	fputs($file, "\$l=\"".$us."\";\n");
            	fputs($file, "\$d=\"".$do."\";\n");
            	fputs($file, "\$s=\"".$co."\";\n");
            	fputs($file, "?>");	
              fclose($file);
          }
      }
       // load a php.ini if one is stored in the db
       $filename = dirname(__FILE__) . "/tfu/php.ini";
       if (!file_exists($filename)) {
         $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'file_php_ini'");
         $file_php_content = $database->loadObjectList();
         if (count($file_php_content) > 0) {
           $content = $file_php_content[0]->value;
           $file = fopen($filename, 'w');
           fputs($file, $content);
           fclose($file);
         }
       }
       // load a .htaccess if one is stored in the db
       $filename = dirname(__FILE__) . "/tfu/.htaccess";
       if (!file_exists($filename)) {
         $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'file_htaccess'");
         $file_htaccess_content = $database->loadObjectList();
         if (count($file_htaccess_content) > 0) {
           $content = $file_htaccess_content[0]->value;
           $file = fopen($filename, 'w');
           fputs($file, $content);
           fclose($file);
         }
       }
      checkSiteFolder($cur_version);
      echo "&nbsp;<br />";
      echo "<div style='text-align:left;'>";
      echo JText::_('I_TEXT');
      if (!defined( '_SELFUPDATE_' )) {
        echo JText::_('I_DESC'); 
      }
      echo "</div>";
    }
  }
  
  function install_db($cur_version){
  $database = JFactory::getDBO();
     
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader");
	  $database->query();
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_user");
	  $database->query();
	  $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_conf");
	  $database->query();
	  
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader_user ( id INT NOT NULL AUTO_INCREMENT, 
	  profile INT,
	  user INT,
	  PRIMARY KEY (id),
	  UNIQUE (profile,user)
	  )");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	   
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader (
	              id INT NOT NULL AUTO_INCREMENT,gid TEXT NOT NULL,
	              config_name TEXT NOT NULL,folder TEXT NOT NULL DEFAULT '',
	              description TEXT NOT NULL DEFAULT '',text_title TEXT DEFAULT '',
	              text_title_lang TEXT DEFAULT '',text_top TEXT DEFAULT '',
	  			  text_top_lang TEXT DEFAULT '',text_bottom TEXT DEFAULT '',
	              text_bottom_lang TEXT DEFAULT '',maxfilesize TEXT NOT NULL,
	              resize_show TEXT NOT NULL,resize_data TEXT ,
	              resize_label TEXT ,resize_default TEXT ,
	              allowed_file_extensions TEXT,forbidden_file_extensions TEXT,
	              hide_remote_view TEXT DEFAULT '',show_delete TEXT NOT NULL,
	              enable_folder_browsing TEXT NOT NULL,enable_folder_creation TEXT NOT NULL,
	              enable_folder_deletion TEXT NOT NULL,enable_folder_rename TEXT NOT NULL,
	              enable_file_rename TEXT NOT NULL,keep_file_extension TEXT NOT NULL,
	              enable_file_download TEXT NOT NULL,sort_files_by_date TEXT NOT NULL,
	              warning_setting TEXT NOT NULL,show_size TEXT DEFAULT '',
	              enable_setting TEXT NOT NULL,creation_date DATE NOT NULL,
	              last_modified_date DATE NOT NULL,fix_overlay TEXT NOT NULL,
	              flash_title TEXT NOT NULL, hide_directory_in_title TEXT NOT NULL, 
	              swf_text TEXT, split_extension TEXT,
	              upload_notification_email TEXT,upload_notification_email_from TEXT,
	              upload_notification_email_subject TEXT,upload_notification_email_text TEXT,
	              upload_finished_js_url TEXT,preview_select_js_url TEXT,
	              delete_js_url TEXT,js_change_folder TEXT,
	              directory_file_limit TEXT,queue_file_limit TEXT,
	              queue_file_limit_size TEXT,display_width TEXT,
	              enable_folder_movecopy TEXT, enable_file_movecopy TEXT,
	              preview_textfile_extensions TEXT,edit_textfile_extensions TEXT,
	              PRIMARY KEY (id))");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
	  $database->setQuery("CREATE TABLE #__joomla_flash_uploader_conf (key_id TEXT, value TEXT)");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
      // we set the version
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('version','".$cur_version."')");
      if(!$database->query()) echo $database->getErrorMsg().'<br />';
	  
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('keep_tables','true')");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	  
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('use_js_include','true')");
	  if(!$database->query()) echo $database->getErrorMsg().'<br />';
	   
      // admin
	  $database->setQuery("INSERT INTO #__joomla_flash_uploader(
       config_name, folder, description, text_title,
	   text_top, text_bottom, text_title_lang, text_top_lang,
	   text_bottom_lang, maxfilesize, resize_show, resize_data,
	   resize_label, resize_default, allowed_file_extensions, forbidden_file_extensions,
	   hide_remote_view, show_delete, enable_folder_browsing, enable_folder_creation,
	   enable_folder_deletion, enable_folder_rename, enable_file_rename, keep_file_extension,
	   enable_file_download, sort_files_by_date, warning_setting, show_size,
	   enable_setting, creation_date, last_modified_date, fix_overlay,
	   flash_title, hide_directory_in_title, upload_notification_email, upload_notification_email_from,
	   upload_notification_email_subject, upload_notification_email_text, upload_finished_js_url, preview_select_js_url,
	   delete_js_url, js_change_folder, directory_file_limit, queue_file_limit, 
       queue_file_limit_size, display_width, enable_folder_movecopy, enable_file_movecopy,
       preview_textfile_extensions, edit_textfile_extensions
     ) 
	   values 
	   ('admin', '','Administration profile','Title',
	   'Text before flash',  'Text after flash',  'true',  'true',
	   'true',  '',  'true',  '100000,1000',
	   'Original,1000',  '0',  'all',  'php',
	   'false',  'true',  'true',  'true',
	   'true',  'true',  'true',  'true',
	   'true',  'false',  'once',  '',
	   'true',  NOW(),  NOW(),  'false',
	   'JFUploader',  'false',  '',  '',
	   'Files were uploaded by the JFUploader',  'The following files where uploaded by %s: %s',  '',  '',
	   '',  '',  '100000',  '100000',
	   '100000',  '650',  'true', 'true',
       'txt,log,php,css,htm,html,js,bak','txt,log,php,css,htm,html,js,bak' );
	   ");
	 if(!$database->query()) echo $database->getErrorMsg().'<br />';
	 
     // frontend user
     $database->setQuery("INSERT INTO #__joomla_flash_uploader(
       config_name, folder, description, text_title,
	   text_top, text_bottom, text_title_lang, text_top_lang,
	   text_bottom_lang, maxfilesize, resize_show, resize_data,
	   resize_label, resize_default, allowed_file_extensions, forbidden_file_extensions,
	   hide_remote_view, show_delete, enable_folder_browsing, enable_folder_creation,
	   enable_folder_deletion, enable_folder_rename, enable_file_rename, keep_file_extension,
	   enable_file_download, sort_files_by_date, warning_setting, show_size,
	   enable_setting, creation_date, last_modified_date, fix_overlay,
	   flash_title, hide_directory_in_title, upload_notification_email, upload_notification_email_from,
	   upload_notification_email_subject, upload_notification_email_text, upload_finished_js_url, preview_select_js_url,
	   delete_js_url, js_change_folder, directory_file_limit, queue_file_limit, 
       queue_file_limit_size, display_width, enable_folder_movecopy, enable_file_movecopy,
       preview_textfile_extensions, edit_textfile_extensions
       ) 
	    values 
	    ('frontend','images/stories','Example frontend profile','Title',
	    'Text before flash','Text below flash','false','false',
	    'false', '1000','true','100000,1000',
        'Original,1000', '0','jpg,jpeg,gif,png' ,'' ,
        'false','true','true','false',
        'false','false', 'false','true',
        'false', 'false','all','',
        'true', NOW(), NOW(), 'false',
        'JFUploader','false', '','',
	    'Files were uploaded by the JFUploader','The following files where uploaded by %s: %s','','',
		'','','100000','100000',
	    '100000','650','false','false',
        'txt,log', '' );
     ");
  if(!$database->query()) echo $database->getErrorMsg().'<br />';
  }
  
  
  /*
    This function checks if a column does already exists. If not it is created.
    This makes it easy to do a update from older versions 
  */
  function update_db($newinstall){
   $database = JFactory::getDBO();
    $error = false;
      // we create the new columns for 2.7
      $error = addColumn($database, 'preview_textfile_extensions', '', $error);
      $error = addColumn($database, 'edit_textfile_extensions', '', $error);
      $error = addColumn($database, 'js_create_folder', '', $error);
      $error = addColumn($database, 'js_rename_folder', '', $error);
      $error = addColumn($database, 'js_delete_folder', '', $error);
      $error = addColumn($database, 'js_copymove', '', $error);      
	  // new 2.8
	  $error = addColumn($database, 'language_dropdown', 'de,en,es,cn,da,fr,it,jp,nl,no,pl,pt,ru,se', $error);
	  $error = addColumn($database, 'use_image_magic', 'false', $error);
	  $error = addColumn($database, 'image_magic_path', 'convert', $error);
	  $error = addColumn($database, 'exclude_directories', 'data.pxp,_vti_cnf,.svn,CVS,thumbs', $error);
	  $error = addColumn($database, 'normalise_file_names', 'false', $error);
	  $error = addColumn($database, 'download_multiple_files_as_zip', 'false', $error);
	  $error = addColumn($database, 'allowed_view_file_extensions', 'all', $error);
	  $error = addColumn($database, 'forbidden_view_file_extensions', '', $error);
	  $error = addColumn($database, 'description_mode', 'false', $error);
	  $error = addColumn($database, 'description_mode_show_default', 'true', $error);
	  $error = addColumn($database, 'description_mode_store', 'email', $error);
	  $error = addColumn($database, 'master_profile', 'false', $error);
	  $error = addColumn($database, 'master_profile_mode', 'login', $error);
	  $error = addColumn($database, 'master_profile_lowercase', 'true', $error);
	  // new 2.8.3
	  $error = addColumn($database, 'normalise_directory_names', 'false', $error);
	  $error = addColumn($database, 'direct_download', 'false', $error);
	  $error = addColumn($database, 'fix_utf8', '', $error);
	  
	  // new 2.9
	  if (!testEntry('use_js_include')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('use_js_include','true')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('backend_access_upload')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('backend_access_upload','Manager')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('backend_access_config')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('backend_access_config','Manager')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('file_chmod')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('file_chmod','')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
      $error = addColumn($database, 'overwrite_files', 'true', $error);
	    $error = addColumn($database, 'description_mode_mandatory', 'false', $error); 
      $error = addColumn($database, 'show_full_url_for_selected_file', 'false', $error);
      $error = addColumn($database, 'normalize_spaces', 'false', $error);
      
    if (!testEntry('dir_chmod')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('dir_chmod','')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  // new 2.10.3
	  if (!testEntry('enable_upload_debug')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('enable_upload_debug','false')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  } // will be part of 2.10.4
	  if (!testEntry('sa_profil')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('sa_profil','1')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('a_profil')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('a_profil','1')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('m_profil')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('m_profil','1')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	  if (!testEntry('enhanced_debug')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('enhanced_debug','false')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
    $error = addColumn($database, 'upload_notification_use_full_path', 'false', $error); 
    
    // new 2.10.6
    $error = addColumn($database, 'hide_hidden_files', 'false', $error); 
    $error = addColumn($database, 'truncate_dir_in_title', 'false', $error);
      
    if (!testEntry('check_image_magic')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('check_image_magic','true')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }  
	  
    // new 2.11
    $error = addColumn($database, 'form_fields', '', $error); 
    $error = addColumn($database, 'hide_hidden_files', 'false', $error); 
    $error = addColumn($database, 'big_progressbar', 'false', $error); 
    $error = addColumn($database, 'img_progressbar', 'progressbar.png', $error); 
    $error = addColumn($database, 'img_progressbar_back', 'progressbar_back.png', $error); 
    $error = addColumn($database, 'img_progressbar_anim', 'progressbar_anim.swf', $error); 
    $error = addColumn($database, 'enable_dir_create_detection', 'true', $error); 
    $error = addColumn($database, 'ftp_enable', 'false', $error); 
    $error = addColumn($database, 'ftp_host', 'host', $error); 
    $error = addColumn($database, 'ftp_port', 21, $error); 
    $error = addColumn($database, 'ftp_user', 'user', $error); 
    $error = addColumn($database, 'ftp_pass', 'pass', $error); 
    $error = addColumn($database, 'ftp_root', 'full root directory', $error); 
    $error = addColumn($database, 'big_server_view', 'false', $error); 
    // new 2.12
    $error = addColumn($database, 'compression', 80, $error); 
    $error = addColumn($database, 'remove_multiple_php_extension', 'true', $error); 
    $error = addColumn($database, 'scan_images', 'true', $error);  
    // new 2.12.1 
    $error = addColumn($database, 'forbidden_view_file_filter', '', $error); 
    $error = addColumn($database, 'zip_file_pattern', 'download-{number}-files_{date}.zip', $error);    
     // 2.13
    $error = addColumn($database, 'info_text', '{dimension} | {size} | {date}', $error); 
    $error = addColumn($database, 'info_textcolor_R', 255, $error); 
    $error = addColumn($database, 'info_textcolor_G', 60, $error); 
    $error = addColumn($database, 'info_textcolor_B', 60, $error); 
    $error = addColumn($database, 'info_font', 'verdana.ttf', $error); 
    $error = addColumn($database, 'info_fontsize', 8, $error);           
    // new 2.14
    $error = addColumn($database, 'directory_file_limit_size', -1, $error);   
    $error = addColumn($database, 'directory_file_limit_size_system', 'true', $error);   
    $error = addColumn($database, 'sort_directores_by_date', 'false', $error);   
    $error = addColumn($database, 'show_server_date_instead_size', 'false', $error);   
    $error = addColumn($database, 'enable_file_creation', 'false', $error);   
    $error = addColumn($database, 'enable_file_creation_extensions', 'txt', $error);   
    $error = addColumn($database, 'zip_folder', '', $error);   
    // new 2.15
     if (!testEntry('idn_url')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('idn_url','')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  } 
    $error = addColumn($database, 'switch_sides', 'false', $error);    
    if (!testEntry('use_index_for_files')) {
      $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('use_index_for_files','true')");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }   
	  $error = addColumn($database, 'date_format', 'd.m.y', $error); 
    
    // new 2.17
    $error = addColumn($database, 'normalize_upper_case', 'false', $error); 
    $error = addColumn($database, 'start_folder', '', $error); 
    $error = addColumn($database, 'use_smtp', 'false', $error); 
    // new 2.18
    $error = addColumn($database, 'use_size_as_height', 'false', $error); 
          
    if ($error) {
      echo "<p class='error'>";
      echo JText::_('I_UPDATE_ERROR');
      return true;
      echo "</p>";
    } else {
      if (!$newinstall) {
        echo JText::_('I_UPDATE_OK');
      }
      return false;
    }            
  }
  
  /*
    Checks if a field is a table exists - true: exist; false: does not exist
  */
  function testField($field, $table) {
     $database = JFactory::getDBO();
     $database->setQuery("show columns from $table like '$field'");
     return (count ($database->loadObjectList()) > 0) ? true : false;
  }
  /*
    Checks if a entry in a table exists - true: exist; false: does not exist
  */
  function testEntry($entry) {
     $database = JFactory::getDBO();
     $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = '".$entry."'");
     return (count ($database->loadObjectList()) > 0) ? true : false;
  }
  
  /*
    Adds a new column - first it checks is the table exists and if not it is added + the default value is set.
  */
  function addColumn($database, $field, $defaultValue, $error) {
    if (!testField($field, "#__joomla_flash_uploader")) {
	    $database->setQuery("ALTER TABLE #__joomla_flash_uploader ADD ".$field." TEXT DEFAULT ''");
	    if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	    // now we have to set the default value because before mysql 5.1.2 this is not possible
        $database->setQuery( "UPDATE #__joomla_flash_uploader SET ".$field."='".$defaultValue."'");
        if(!$database->query()) { $error=true; echo $database->getErrorMsg().'<br />'; }
	  }
	 return $error; 
  }
  
  function checkSiteFolder($cur_version) {
    // define both folders
    $admin_tfu = dirname(__FILE__) . '/tfu';
    $site_tfu = dirname(__FILE__) . '/../../../components/com_jfuploader/tfu'; 
    // check if site exists - if no - do nothing
    if (file_exists($site_tfu)) {
      echo '<p>tfu folder in site found. Updating this folder.</p>';
      // if copy all files to the site 
      jfu_recurse_copy($admin_tfu,$site_tfu);
      // remove all 2.x swf files in admin
      $files = glob($site_tfu . '/tfu_*.swf');
      foreach($files as $file){
         if (basename($file) != 'tfu_'.$cur_version.'.swf') {
             unlink($file);
         }
      };
      // remove the tfu folder in the admin
      rrmdir($admin_tfu);
    } else {
      // remove all 2.x swf files in admin
      $files = glob($admin_tfu . '/tfu_*.swf');
      foreach($files as $file){
         if (basename($file) != 'tfu_'.$cur_version.'.swf') {
             unlink($file);
         }
      };
    }       
  }
  
  function jfu_recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                jfu_recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
  }
  
  function rrmdir($dir) {
     $files = glob($dir . '/{,.ht}*', GLOB_BRACE);
     foreach($files as $file) {
         if(is_dir($file))
             rrmdir($file);
         else
             unlink($file);
     }
     rmdir($dir);  
 } 
  
  
?>