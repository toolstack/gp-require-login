<?php
/*
 * Plugin Name: GP Require Login
 * Plugin URI: http://glot-o-matic.com/gp-require-login
 * Description: Redirect non-logged in users to the login page.
 * Version: 0.6
 * Author: GregRoss
 * Author URI: http://toolstack.com
 * Tags: glotpress, glotpress plugin, translate
 * License: GPLv2 or later
 */

class GP_Require_Login {
	public $id = 'require-login';

	public function __construct() {

	add_filter( 'gp_router_http_methods', array( $this, 'gp_router_http_methods' ) );
	}
	
	public function gp_router_http_methods( $methods ) {
		if( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( $this->get_current_page_url() ) );
			exit;
		}
		
		return $methods;
	}
	
	private function get_current_page_url() {
		$page_url = 'http';
		
		// Ask WordPress if we're running over SSL.
		$ssl = is_ssl();
		
		// Double check if we're running behind a broken load balancer.
		if( false === $ssl ) {
			if( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
				$ssl = true;
			}
		}
		
		// Add the 's' to the url if required.
		if( $ssl ) {
			$page_url .= 's';
		}
		
		// Add the server name to the url.
		$page_url .= '://' . $_SERVER['SERVER_NAME'];
		
		// Check to see if we're running over a non-default port number, if so add it to the url.
		if( ( ! $ssl && '80' !== $_SERVER['SERVER_PORT'] ) || ( $ssl && '443' !== $_SERVER['SERVER_PORT'] ) ) {
			$page_url .= ':' . $_SERVER['SERVER_PORT'];
		}

		// And finally add the URI to the URL.
		$page_url .= $_SERVER['REQUEST_URI'];
		
		return $page_url;
	}
	
}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_require_login_init' );

// This function creates the plugin.
function gp_require_login_init() {
	GLOBAL $gp_require_login;
	
	$gp_require_login = new GP_Require_Login;
}
