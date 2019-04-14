<?php
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Base;

use Inc\Base\BaseController;

class Settings extends BaseController
{
	public function register() 
	{
		add_filter( "plugin_action_links_" . $this->plugin, array( $this, 'settingsLink' ) );
	}

	public function settingsLink( $links ) 
	{
		$settings_link = '<a href="options-general.php?page=header_body">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}