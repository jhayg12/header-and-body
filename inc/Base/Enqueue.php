<?php 
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Base;

use Inc\Base\BaseController;

class Enqueue extends BaseController
{	
	/**
	 * Register Enqueue
	 */
	public function register() 
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Version Ids for reloading of Scripts/ Styles
	 */
	public function version_id() {
		if ( WP_DEBUG ) {
			return time();
		}
		
		return VERSION;
	}
	
	/**
	 * Enqueue Scripts / Styles
	 */
	public function enqueue($hook) 
	{	

		if  ( 'settings_page_header_body' != $hook && 'settings_page_add_header' != $hook &&
				'settings_page_add_body' != $hook && 'settings_page_edit_header' != $hook &&
				'settings_page_edit_body' != $hook && 'settings_page_view_header' != $hook &&
				'settings_page_view_body' != $hook && 'settings_page_delete_header' != $hook &&
				'settings_page_delete_body' != $hook  ) {
			return;
		}

		wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
		wp_enqueue_style( 'styles', $this->plugin_url . '/assets/style.css', '', array( $this, 'version_id' ) );
		wp_enqueue_script( 'scripts', $this->plugin_url . '/assets/script.js', array('jquery'), array( $this, 'version_id' ), true );
		wp_enqueue_script( 'jquery' );

	}
}