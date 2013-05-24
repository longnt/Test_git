<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<!-- Title -->
	<title>
	<?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 */

		global $page, $paged,$classbody, $mobile_browser;
		
		wp_title( '|', true, 'right' );

		// Add the blog name.
		bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";

		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			echo ' | ' . sprintf( __( 'Page %s', 'sexulator' ), max( $paged, $page ) );

		?>
	</title>
	<!-- Meta tags -->
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<!-- social sharing metadata -->
	<meta property="og:title" content="Sexulator" />
	<meta property="og:description" content="	Who. What. Where. When. WOW! Keep a record of your hot nights, prove to you and your partner how much you do it and spice up your sex life. Take the guesswork out of the bedroom.!" />
	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/preview-1.png" />

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/foundation.css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/styles.css" />
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/skin.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" type="text/javascript"></script>
	<link href='http://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/custom.modernizr.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.jcarousel.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/home.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url');?>" media="screen" />
</head>
<body>
	<!-- BEGIN .main-body-wrapper -->
	<div class="main-body-wrapper"> 
		<header class="page-width main-header border-radius-0011">
			<nav class="top-bar main-menu">
				<ul class="title-area">
					<!-- Title Area -->
					<li class="name">
						<a href="<?php bloginfo('home'); ?>" title="MySexulator"><img src="<?php echo get_template_directory_uri(); ?>/images/logo.png"></a>
					</li>
				</ul>
				<section class="top-bar-section">
				<!-- Right Nav Section -->
					<ul class="right">
						<li> <a href="events-calendar.html"><span>My Events</span></a> </li>
						<li> <a href="join.html"><span>Join</span></a> </li>
						<li> <a href="link.html"><span>Links</span></a> </li>
						<li> <a href="aboutus.html"><span>About Us</span></a> </li>
						<li> <a href="gallery.html"><span>Gallery</span></a> </li>
						<li> <a href="login.html"><span>Login</span></a> </li>
					</ul>
				</section>
			</nav>
			
		</header>