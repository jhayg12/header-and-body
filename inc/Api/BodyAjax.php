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
$table_name = $wpdb->prefix . 'body_data_plugin';

// Handles the deletion of records
if ( ! empty ( $_POST[ 'bid' ] ) ) {
    $bid = $_POST[ 'bid' ];
    $wpdb->query(" DELETE FROM {$table_name} WHERE id = {$bid} ");
}

// Handles the positioning of records
if ( ! empty ( $_POST[ 'bPosId' ] ) ) {
    $bPosId = $_POST[ 'bPosId' ];
    $obj_data = $wpdb->get_results(" SELECT position FROM {$table_name} WHERE id = {$bPosId} LIMIT 1");
    $obj_temp = json_decode( json_encode( $obj_data ), true );

    if ( $obj_temp[0]['position'] == 0 ) {
        $wpdb->query(" UPDATE {$table_name} SET position = 1 WHERE id = {$bPosId} ");
    } else {
        $wpdb->query(" UPDATE {$table_name} SET position = 0 WHERE id = {$bPosId} ");
    }

}

// Handles body Top Positioning
if ( ! empty ( $_POST[ 'bUpId' ] ) ) {
    $bUpId = $_POST[ 'bUpId' ];
    $topIds = array();
    $bottomIds = array();
    
    // Get all the TOP body_order in ASC order just like it was presented on the table
    $top_obj_data = $wpdb->get_results(" SELECT body_order FROM {$table_name} WHERE position = 0 ORDER BY body_order ASC");
    $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

    for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
        array_push( $topIds, $top_obj_temp[$x]['body_order'] );
    }

    // Get all the TOP body_order in ASC order just like it was presented on the table
    $bottom_obj_data = $wpdb->get_results(" SELECT body_order FROM {$table_name} WHERE position = 1 ORDER BY body_order ASC");
    $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

    for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
        array_push( $bottomIds, $bottom_obj_temp[$x]['body_order'] );
    }

    // Get the original body_order value of the selected row and the key of the array $topIds 
    $orig_obj_data = $wpdb->get_results(" SELECT position, body_order FROM {$table_name} WHERE id = {$bUpId} LIMIT 1");
    $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
    
    // Check if the selected record is in TOP position then process
    if ( $orig_obj_temp[0]['position'] == 0) {

        $orig_key = array_search( $orig_obj_temp[0]['body_order'], $topIds );
        $orig_value = $topIds[ $orig_key ];

        if ( $orig_key > 0 ) {
            $temp_key = (int)$orig_key - 1; 
            $temp_value = $topIds[ $temp_key ]; 

            // Get the id of the element above of the selected element
            $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$table_name} WHERE body_order = $temp_value LIMIT 1");
            $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

            $temp_id = $temp_obj_temp[0]['id']; 

            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $temp_value WHERE id = {$bUpId} ");
            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $orig_value WHERE id = {$temp_id} ");
        }

    } else {

        // Process if position is Bottom
        $orig_key = array_search( $orig_obj_temp[0]['body_order'], $bottomIds );
        $orig_value = $bottomIds[ $orig_key ];

        if ( $orig_key > 0 ) {
            $temp_key = (int)$orig_key - 1; 
            $temp_value = $bottomIds[ $temp_key ]; 

            // Get the id of the element above of the selected element
            $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$table_name} WHERE body_order = $temp_value LIMIT 1");
            $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

            $temp_id = $temp_obj_temp[0]['id']; 

            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $temp_value WHERE id = {$bUpId} ");
            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $orig_value WHERE id = {$temp_id} ");
        }

    }

}

// ************************************************************

// Handles body Top Positioning
if ( ! empty ( $_POST[ 'bDownId' ] ) ) {
    $bDownId = $_POST[ 'bDownId' ];
    $topIds = array();
    $bottomIds = array();
    
    // Get all the TOP body_order in ASC order just like it was presented on the table
    $top_obj_data = $wpdb->get_results(" SELECT body_order FROM {$table_name} WHERE position = 0 ORDER BY body_order ASC");
    $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

    for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
        array_push( $topIds, $top_obj_temp[$x]['body_order'] );
    }

    // Get all the TOP body_order in ASC order just like it was presented on the table
    $bottom_obj_data = $wpdb->get_results(" SELECT body_order FROM {$table_name} WHERE position = 1 ORDER BY body_order ASC");
    $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

    for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
        array_push( $bottomIds, $bottom_obj_temp[$x]['body_order'] );
    }

    // Get the original body_order value of the selected row and the key of the array $topIds 
    $orig_obj_data = $wpdb->get_results(" SELECT position, body_order FROM {$table_name} WHERE id = {$bDownId} LIMIT 1");
    $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
    
    // Check if the selected record is in TOP position then process
    if ( $orig_obj_temp[0]['position'] == 0) {

        $orig_key = array_search( $orig_obj_temp[0]['body_order'], $topIds );
        $orig_value = $topIds[ $orig_key ];

        $keys = array_keys( $topIds );
        $last_key = end( $keys );

        if ( $orig_key < $last_key ) {
            $temp_key = (int)$orig_key + 1; 
            $temp_value = $topIds[ $temp_key ]; 

            // Get the id of the element above of the selected element
            $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$table_name} WHERE body_order = $temp_value LIMIT 1");
            $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

            $temp_id = $temp_obj_temp[0]['id']; 

            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $temp_value WHERE id = {$bDownId} ");
            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $orig_value WHERE id = {$temp_id} ");
        }

    } else {

        // Process if position is Bottom
        $orig_key = array_search( $orig_obj_temp[0]['body_order'], $bottomIds );
        $orig_value = $bottomIds[ $orig_key ];

        $keys = array_keys( $bottomIds );
        $last_key = end( $keys );

        if ( $orig_key < $last_key ) {
            $temp_key = (int)$orig_key + 1; 
            $temp_value = $bottomIds[ $temp_key ]; 

            // Get the id of the element above of the selected element
            $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$table_name} WHERE body_order = $temp_value LIMIT 1");
            $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

            $temp_id = $temp_obj_temp[0]['id']; 

            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $temp_value WHERE id = {$bDownId} ");
            $wpdb->get_results(" UPDATE {$table_name} SET body_order = $orig_value WHERE id = {$temp_id} ");
        }

    }

}




