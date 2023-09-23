<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="wrapper"><div id="wrap">
	<div id="header">
	<div id="head" class="clearfix">
		<div id="top-panel" class="tc-full fmid padded-hor"></div>
	
		<div id="mid-panel" class="tc-full fmid padded-hor clearfix"><img id="logo" class="" src="<?php echo BIT_URL."/images/formlogo.jpg";?>" /></div>

		<div id="bot-panel" class="tc-full fmid padded-hor">
			<?php display_menu("primary");?>
		</div>
	</div>
	</div> <!-- #header, #head -->