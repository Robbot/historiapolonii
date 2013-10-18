<?php
/**
 * JFlUploader 3.1 Freeware - for Joomla 1.5.x
 *
 * Copyright (c) 2004-2013 TinyWebGallery
 * written by Michael Dempfle
 *
 * @license GNU / GPL 
 *    
 * For the latest version please go to http://jfu.tinywebgallery.com
**/
  defined( '_JEXEC' ) or die( 'Restricted access' );
  
  // uninstall screen
define("R_TEXT","<p>JFUploader was uninstalled successful.</p>");
define("R_TEXT_KEEP","<p>The tables used for the profiles and the users mapping where not removed. If you like to remove this tables too please reinstall the component, go to Components -> JFUploader -> Configuration and change the global setting to remove the settings as well. If you only updating JFU you should keep the tables because the setup will update them if needed.</p>");
define("R_TEXT_NOT_KEEP","<p>Removing the tables ...<br>done.<br>If you want to keep your settings the next time (if you only want to update!), go to Components -> JFUploader -> Configuration and change the global settings. Tables which where created by plugins (e.g. the database logger) have to be deleted manually.</p>");

  function com_uninstall() {
      $database = JFactory::getDBO();
	
	  $database->setQuery("SELECT value FROM #__joomla_flash_uploader_conf where key_id = 'keep_tables'");
	  $settings = $database->loadObjectList();
 
	  if ( $settings[0]->value == 'false') {
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader");
	    $database->query();
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_user");
	    $database->query();
	    $database->setQuery("DROP TABLE IF EXISTS #__joomla_flash_uploader_conf");
        $database->query();
        echo R_TEXT_NOT_KEEP;
      } else { // we keep it and store the license data if still a license file exists.
        $database->setQuery("DELETE FROM #__joomla_flash_uploader_conf where key_id='user' OR key_id='domain' OR key_id='code' OR key_id='file_php_ini' OR key_id='file_htaccess' ");
        if(!$database->query()) { error_log($database->getErrorMsg()); }
        // If we have a license file we add this stuff to the db - we delete the old one first
	   $licexists = true;
        $file =  dirname(__FILE__) . '/tfu/twg.lic.php';
        $file2 = dirname(__FILE__) . '/../../../components/com_jfuploader/tfu/twg.lic.php';
        if (file_exists($file)) {
          include($file);
        } else if (file_exists($file2)) {
          include($file2);
        } else {
          $licexists = false;
        }
        if ($licexists) {
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('user','".$l."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('domain','".$d."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('code','".$s."')");
  	      if(!$database->query()) { error_log($database->getErrorMsg()); }	  
        }
        
        // php.ini
        $file_php =  dirname(__FILE__) . '/tfu/php.ini';
        $file_php2 = dirname(__FILE__) . '/../../../components/com_jfuploader/tfu/php.ini';
        $phpexists = true;  
        if (file_exists($file_php)) {
          $content = file_get_contents($file_php);
        } else if (file_exists($file_php2)) {
          $content = file_get_contents($file_php2); 
        } else {
          $phpexists = false;  
        }  
        if ($phpexists) {  
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('file_php_ini','".$content."')");
  	     if(!$database->query()) { error_log($database->getErrorMsg()); }	
        }
        
        // .htaccess
        $file_htaccess =  dirname(__FILE__) . '/tfu/.htaccess';
        $file_htaccess2 = dirname(__FILE__) . '/../../../components/com_jfuploader/tfu/.htaccess';
        $htexists = true;  
        if (file_exists($file_htaccess)) {
          $content = file_get_contents($file_htaccess);
        } else if (file_exists($file_htaccess2)) {
          $content = file_get_contents($file_htaccess2); 
        } else {
          $htexists = false;  
        }      
        if ($htexists) {     
          $database->setQuery("INSERT INTO #__joomla_flash_uploader_conf (key_id, value) values ('file_htaccess','".$content."')");
  	     if(!$database->query()) { error_log($database->getErrorMsg()); }	
        }
        echo R_TEXT_KEEP;
      }
    echo R_TEXT;
  }
?>