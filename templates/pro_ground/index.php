<?php defined( "_VALID_MOS" ) or die( "Direct Access to this location is not allowed." );$iso = split( '=', _ISO );echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php mosShowHead(); ?>
<meta http-equiv="Content-Type" content="text/html" <?php echo _ISO; ?>" />
<?php if ( $my->id ) { initEditor(); } ?>
<?php echo "<link rel=\"stylesheet\" href=\"$GLOBALS[mosConfig_live_site]/templates/$GLOBALS[cur_template]/css/template_css.css\" type=\"text/css\"/>" ; ?>

<link rel="alternate" title="<?php echo $mosConfig_sitename; ?>" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/index2.php?option=com_rss&no_html=1" type="application/rss+xml" />
<link rel="alternate" type="application/rss+xml" title="<?php echo $mosConfig_sitename?>" href="<?php echo $mosConfig_live_site;?>/index.php?option=com_rss&feed=RSS2.0&no_html=1" />
<!--[if lt IE 7]>
<?php echo "<link rel=\"stylesheet\" href=\"$GLOBALS[mosConfig_live_site]/templates/$GLOBALS[cur_template]/css/template_css_ie.css\" type=\"text/css\"/>" ; ?>
<![endif]-->
</head>
<body>
	<div class="spacer15"></div>
    
  <div id="container">
		<div id="search_container">
	  <div id="search">
      			<div id="datka"><?php echo mosCurrentDate(); ?></div>
				<?php mosLoadModules('user4'); ?>
		  </div>
	  </div>
		<div class="spacer15"></div>
		
        <div id="header">
			<div id="title_container">
              <div id="title">
                	<table width="100%"><td width="50%" style="font:bold 24px Tahoma, Verdana, sans-serif;">                	
					<?php echo $GLOBALS['mosConfig_sitename']?></td>
                 <td width="50%">
					<?php mosLoadModules( 'banner', -1 ); ?>                  </td>
                  </table>
              </div>
		  </div>
		</div>
          	
	<div class="spacer15"></div>
		<div id="top_menu_container">
			<?php mosLoadModules('user3'); ?>
		</div>
		<div class="spacer15"></div>
		<div id="contents">
			<div id="container1">
				<div id="container1_core">
				  <?php mosLoadModules('left'); ?>
				</div>
		  </div>
			<div id="container2">
				<div id="container2_border">
				<div id="container2_core">
					<div id="pathway_text">
						<?php mosPathWay(); ?>
					</div>
					<?php mosMainBody(); ?>
					
					<?php
		  			if ( mosCountModules( 'user1' ) + mosCountModules( 'user2' ) > 0){
		  			?>
					<table id="news_popular" border="0" cellspacing="10" cellpadding="0">
  						<tr>
    						<?php if ( mosCountModules( 'user1' ) > 0){?>
								<td><?php mosLoadModules ( 'user1', -2 ); ?></td>
							<?php
		  					}
		  					?>
							<?php if ( mosCountModules( 'user2' ) > 0){?>
    							<td><?php mosLoadModules ( 'user2', -2 ); ?></td>
							<?php
		  					}
		  					?>
  						</tr>
					</table>
					<?php
		  			}
		  			?>
				</div>
				</div>
			</div>
			<div id="container3">
				<div id="container3_core">
					<?php mosLoadModules('top'); ?>
					<?php mosLoadModules('right'); ?>
				</div>
			</div>
			<div class="spacer"></div>
		</div>
		<div class="spacer15"></div>	
		<div id="footer">
			Powered by <a href="http://joomla.org/" class="sgfooter" target="_blank">Joomla!</a>. Designed & maintained by Robert Rojek
		</div>
		<div class="spacer15"></div>
	</div>
<?php mosLoadModules( 'debug', -1 );?>
</body>
</html>
