<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  Header and Body Tag Injection Plugin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;
$header_table_name = $wpdb->prefix . 'header_data_plugin';
$body_table_name = $wpdb->prefix . 'body_data_plugin';

// Drop header_data_plugin table
$wpdb->query( "DROP TABLE IF EXISTS {$header_table_name}" );

// Drop body_data_plugin table
$wpdb->query( "DROP TABLE IF EXISTS {$body_table_name}" );