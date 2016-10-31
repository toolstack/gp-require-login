<?php
/*
Plugin Name: GP Require Login
Plugin URI: http://glot-o-matic.com/gp-require-login
Description: Redirect non-logged in users to the login page.
Version: 0.5
Author: GregRoss
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin, translate
License: GPLv2 or later
*/

class GP_Require_Login {
	public $id = 'require-login';

	public function __construct() {

	add_filter( 'gp_router_http_methods', array( $this, 'gp_router_http_methods' ) );
	}
	
	public function gp_router_http_methods( $methods ) {
		if( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
			exit;
		}
		
		return $methods;
	}
	
}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_require_login_init' );

// This function creates the plugin.
function gp_require_login_init() {
	GLOBAL $gp_require_login;
	
	$gp_require_login = new GP_Require_Login;
}
