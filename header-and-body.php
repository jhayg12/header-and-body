<?php
/**
 * @package  Header and Body Tag Injection Plugin
 */
/*
Plugin Name: Header and Body Tag Injection
Description: The purpose of this plugin is to be able to inject any code (HTML, CSS, Script) in the head and at the bottom of the body tag of any Wordpress page.
Version: 1.0.0
Author: IScale Solutions Inc.
Author URI: http://iscale-solutions.com
License: GPLv2 or later
Text Domain: header-and-body-plugin
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// Require once the Initilization File
if ( file_exists( dirname( __FILE__ ) . '/inc/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/inc/init.php';
}

// Define CONSTANTS
define ( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define ( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define ( 'PLUGIN', plugin_basename( __FILE__ ) );
define ( 'VERSION', '1.1' );
define ( 'REDIRECT_LINK', get_site_url() . '/wp-admin/options-general.php?page=header_body' );

use Inc\Base\CreateTables;

/**
 * The code that runs during plugin activation
 */
function create_tables()
{
	$create_tables = new CreateTables();
	$create_tables->createHeaderTable();
	$create_tables->createBodyTable();
	// $create_tables->sampleHeaderData();
	// $create_tables->sampleBodyData();
}

function activate_header_and_body() 
{	
	Inc\Base\Activate::activate();
	create_tables();
}

register_activation_hook( __FILE__, 'activate_header_and_body' );

/**
 * The code that runs during plugin deactivation
 */
function deactivate_header_and_body() 
{
	Inc\Base\Deactivate::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_header_and_body' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Inc\\Init' ) ) {
	Inc\Init::register_services();
} 


