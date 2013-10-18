<?php

defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$document->addScript( JURI::root() . 'modules/mod_artuploader/js/ajaxupload.js' ); 
$document->addScript( JURI::root() . 'modules/mod_artuploader/js/jquery.js' ); 
$document->addScript( JURI::root() . 'modules/mod_artuploader/js/jquery-ui.js' ); 
$document->addStyleSheet( JURI::root() . 'modules/mod_artuploader/css/jquery-ui.css' ); 

$str_exp = explode("\\", JPATH_SITE);
$str_imp = implode("\\\\", $str_exp);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script type= "text/javascript">/*<![CDATA[*/
$(document).ready(function(){
    <?php
        $relPathRaw = $params->get("relativePath");
        $relPathStrExp = explode("\\", $relPathRaw);
        $relPathRaw = implode("\\\\", $relPathStrExp);
        $relPathStrExp = explode("/", $relPathRaw);
        $relPathRaw = implode("\\\\", $relPathStrExp);
        $relPath = $relPathRaw;
    ?>
    var strAction = 'modules/mod_artuploader/upload.php?path=<?php echo $str_imp . "\\\\" . $relPath . "\\\\" ?>';
    new AjaxUpload('#upload_button', {
        action: strAction,
        name: 'userfile'
    });		

});/*]]>*/</script>

</head>

<body>
  <div style="background-color:#082E89; padding: 2px 2px 2px 2px">
  <form action="#" method="post" style="background-color: white; padding: 10px 10px 10px 10px">
    <table border="0">
      <tr><td><p>Image source:</p></td></tr>
      <tr><td><input size="8" id="upload_button" type="file" /></td></tr>
    </table>
  </form>
  </div>
</body>
</html>
