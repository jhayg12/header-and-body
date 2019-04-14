<?php

$url = ( ! empty ( $_SERVER[ 'HTTPS' ] ) ) ? "https://" . 
    $_SERVER[ 'SERVER_NAME' ] . 
    $_SERVER[ 'REQUEST_URI' ] : "http://" . 
    $_SERVER['SERVER_NAME'] . 
    $_SERVER['REQUEST_URI'];

$url = $_SERVER[ 'REQUEST_URI' ];
$my_url = explode( 'wp-content' , $url ); 
$path = $_SERVER[ 'DOCUMENT_ROOT' ] . "/" . $my_url[0];

include_once $path . '/wp-config.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb;

// Header
if ( ! empty ( $_POST[ 'hIds' ] ) ) {
    $header_data = $_POST[ 'hIds' ];
    $table_name = $wpdb->prefix . 'header_data_plugin';

    for ( $x = 0; $x <= count( $header_data ); $x++ ) {
        $hid = $header_data[$x];
        $wpdb->query("DELETE FROM {$table_name} WHERE id = $hid");
    }

}

// Body
if ( ! empty ( $_POST[ 'bIds' ] ) ) {
    $body_data = $_POST[ 'bIds' ];
    $table_name = $wpdb->prefix . 'body_data_plugin';

    for ( $x = 0; $x <= count( $body_data ); $x++ ) {
        $bid = $body_data[$x];
        $wpdb->query("DELETE FROM {$table_name} WHERE id = $bid");
    }
}