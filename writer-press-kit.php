<?php
/*
 * Plugin Name: Writer Press Kit
 * Version: 1.0.0
 * Plugin URI: http://cato.io/writer-press-kit
 * Description: Enables book authors to easily produce a professional-quality press kit
 * Author: Jamel Cato
 * Author URI: http://cato.io
 * Requires at least: 4.3
 * Tested up to: 4.6
 *
 * Text Domain: writer-press-kit
 *
 * @package WordPress
 * @author Jamel Cato
 * @since 1.0.0
 */
 
 
 /* Prevent direct access outside of WordPress */
function_exists( 'add_action' ) OR exit;

 
/**
 * Define constants
 * @return none
 */
Define( 'WPK_ROOT_856', plugin_dir_path( __FILE__ ) );
Define( 'WPK_URL_856', plugin_dir_url( __FILE__ ) );
Define( 'WPK_SETTINGS_856', admin_url( "tools.php?page=writer-press-kit" ) ); 
Define( 'WPK_VERSION_856', '1.0.0' );
Define( 'WPK_LOADED_856', 'Loaded' ); 

 

/**
 * Load Plugin Files
 *@return none
 */
require_once  __DIR__ . '/inc/wpk-class.php' ;


// Only load the CMB2 library if it is not already installed elsewhere
If ( ! defined ( 'CMB2_LOADED'  ) ) {
		require_once  __DIR__ . '/lib/cmb2/init.php'; 	   
}




/**
* Add Settings Link to Plugin List
* @return array
*/
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'wpk_add_settings_link' );

function wpk_add_settings_link( $links ) {
    $settings_link = '<a href="'.admin_url( "tools.php?page=writer-press-kit" ).'">' . __( 'Settings', 'plan-my-novel' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}


 

/**
* Load Plugin Text Domain
* @return none
*/
add_action( 'init', 'wpk_load_textdomain' );
  
function wpk_load_textdomain() {
  load_plugin_textdomain( 'writer-press-kit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}



 /**
  * Set default settings values 
  * @return none
  */
$wpk_options = get_option( 'writer-press-kit' );

if ( !$wpk_options ) {
	
	$wpk_defaults = array();
		
	$wpk_defaults['wpk_fld_display_bio'] 				= 'on';	
	$wpk_defaults['wpk_fld_display_photos'] 		= 'on';		
	$wpk_defaults['wpk_fld_display_formats'] 		= 'on';
	$wpk_defaults['wpk_fld_display_questions'] 	= 'on';	
	$wpk_defaults['wpk_fld_display_rep'] 				= 'on';	
	
	update_option( 'witer-press-kit', $wpk_defaults );

}