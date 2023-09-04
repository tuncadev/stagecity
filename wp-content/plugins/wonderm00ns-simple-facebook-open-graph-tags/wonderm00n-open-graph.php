<?php
/**
 * Plugin Name: Open Graph and Twitter Card Tags
 * Plugin URI:
 * Description: Improve social media sharing by inserting Facebook Open Graph, Twitter Card and SEO Meta Tags on your WordPress website pages, posts, WooCommerce products, or any other custom post type.
 * Version: 3.3.0
 * Author: WPExperts
 * Author URI: https://wpexperts.io/
 * Text Domain: wonderm00ns-simple-facebook-open-graph-tags
 * Domain Path: /lang
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * WC requires at least: 3.0
 * WC tested up to: 6.6
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WEBDADOS_FB_VERSION', '3.3.0' );
define( 'WEBDADOS_FB_PLUGIN_NAME', 'Open Graph and Twitter Card Tags' );
define( 'WEBDADOS_FB_W', 1200 );
define( 'WEBDADOS_FB_H', 630 );

/* Include core class */
require plugin_dir_path( __FILE__ ) . 'includes/class-webdados-fb-open-graph.php';

/* Uninstall hook */
register_uninstall_hook(__FILE__, 'webdados_fb_uninstall');
function webdados_fb_uninstall() {
	$options = get_option( 'wonderm00n_open_graph_settings' );
	if ( intval($options['fb_keep_data_uninstall'])==0 ) {
		//Settings
		delete_option( 'wonderm00n_open_graph_settings' );
		delete_option( 'wonderm00n_open_graph_version' );
		delete_option( 'wonderm00n_open_graph_admin_notice' );
		global $wpdb;
		//Post meta
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_webdados_fb_open_graph%'" );
		//Transients - Image size cache
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%webdados_og_image_size_%'" );
	}
}

/* Run it - This function name is used by the PRO add-on to detect this plugin */
function webdados_fb_run() {
	if ( $webdados_fb = new Webdados_FB( WEBDADOS_FB_VERSION ) ) {
		return $webdados_fb;
	} else {
		return false;
	}

}

$webdados_fb = webdados_fb_run();