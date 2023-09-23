<?php
if(!defined("BIT_DIR")){define("BIT_DIR", get_template_directory());}
if(!defined("BIT_URL")){define("BIT_URL", get_template_directory_uri());}
if ( ! function_exists( 'bit_setup' ) ) :
function bit_setup() {
	load_theme_textdomain( 'beauisha', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	//add_theme_support( 'woocommerce' );
	//set_post_thumbnail_size( 825, 510, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'beauisha' ),
		'account' => __( 'Account Menu',      'beauisha' ),
		'social'  => __( 'Social Links Menu', 'beauisha' ),
	) );

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );*/
}
endif; // beauisha_setup
add_action( 'after_setup_theme', 'bit_setup' );

function bit_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'beauisha' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Non WC Sidebar', 'beauisha' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Top', 'beauisha' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets here to appear in your footer top.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Bottom', 'beauisha' ),
		'id'            => 'footer-2',
		'description'   => __( 'Add widgets here to appear in your footer bottom.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	/*register_sidebar( array(
		'name'          => __( 'Footer  Widgets#1', 'beauisha' ),
		'id'            => 'footw-1',
		'description'   => __( 'Add widgets here to appear in your footer Widgets#1 Area.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer  Widgets#2', 'beauisha' ),
		'id'            => 'footw-2',
		'description'   => __( 'Add widgets here to appear in your footer Widgets#2 Area.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Widgets#3', 'beauisha' ),
		'id'            => 'footw-3',
		'description'   => __( 'Add widgets here to appear in your footer Widgets#3 Area.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Widgets#4', 'beauisha' ),
		'id'            => 'footw-4',
		'description'   => __( 'Add widgets here to appear in your footer Widgets#4 Area.', 'beauisha' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );*/
}
add_action( 'widgets_init', 'bit_widgets_init' );

if ( ! function_exists( 'bit_fonts_url' ) ) :
/**
 * Register Google fonts for Twenty Fifteen.
 */
function bit_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Sans font: on or off', 'beauisha' ) ) {
		$fonts[] = 'Noto Sans:400italic,700italic,400,700';
	}

	if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'beauisha' ) ) {
		$fonts[] = 'Noto Serif:400italic,700italic,400,700';
	}

	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'beauisha' ) ) {
		$fonts[] = 'Inconsolata:400,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'beauisha' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * JavaScript Detection.
 */
function bit_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'bit_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 */
function bit_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'bit-fonts', bit_fonts_url(), array(), null );

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.2' );
	wp_enqueue_style( 'select22', BIT_URL.'/assets/select2.min.css', array(), '3.2' );

	// Load our main stylesheet.
	wp_enqueue_style( 'bit-style', get_stylesheet_uri() );

	// Load our custom stylesheet.
	//wp_enqueue_style( 'bit-slick1', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css' );
	//wp_enqueue_style( 'bit-slick2', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css' );
	wp_enqueue_style( 'bit-css', BIT_URL.'/css.css' );

	// Load our menu stylesheet.
	//wp_enqueue_style( 'beauisha-menu', BIT_URL.'/menus.css' );

	wp_enqueue_script('jquery');
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'jquery-ui' );

	// Load our custom script.
	//wp_enqueue_script( 'bit-slick3', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js' );
	wp_enqueue_script( 'select23', BIT_URL.'/assets/select2.min.js' );
	wp_enqueue_script( 'bit-js', BIT_URL.'/assets/script.js' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bit_scripts' );

/*******************************************************************************************************************/
function display_menu($name, $type='page', $depth=3, $fish=true, $effect='fade'){
	wp_nav_menu('depth='.$depth.'&theme_location='.$name.'&items_wrap=<a class="mobile-toggle main bicon genericon-menu">Menu</a><ul id="%s" class="%s">%s<ul>&container_class=menu-'.$name.'-container clearfix&menu_class=menus menu-'.$name.'&fallback_cb=regulus_'.$type.'menu_default&item_spacing=discard&after=<span class="mobile-toggle sub"></span>');
}

function regulus_pagemenu_default($args) {
	$n = $args['theme_location'];
	$N = ucwords($n);
	$m0n = strtolower("menu-".$n);
	$m1n = strtolower("menu_".$n);
	echo '<div class="'.$m0n.'-container bicon genericon-menu clearfix">';
		echo '<ul class="menus '.$m0n.'">';
			echo '<li class="'.((is_home() || is_front_page())? 'current_page_item':'').'"><a href="'.home_url().'">'
			.__('Home','RegulusReign').'</a></li>';
				wp_list_pages('depth='.$args['depth'].'&sort_column=menu_order&title_li=');
		echo '</ul>';
	echo '</div>';
}

function regulus_categorymenu_default($args) {
	$n = $args['theme_location'];
	$N = ucwords($n);
	$m0n = strtolower("menu-".$n);
	$m1n = strtolower("menu_".$n);
	echo '<div class="'.$m0n.'-container bicon genericon-menu clearfix">';
		echo '<ul class="menus '.$m0n.'">';
			echo '<li class="'.((is_home() || is_front_page())? 'current_page_item':'').'"><a href="'.home_url().'">'
			.__('Home','RegulusReign').'</a></li>';
				wp_list_categories('depth='.$args['depth'].'&hide_empty=0&orderby=name&show_count=0&use_desc_for_title=1&title_li=');
		echo '</ul>';
	echo '</div>';
}
?>