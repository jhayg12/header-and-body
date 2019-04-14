<?php

$plugin_path = plugin_dir_path( dirname( __FILE__, 4 ) );

require_once( $plugin_path . "header-and-body/inc/Base/Validation/Redirect.php" );

function insert_to_database( $table_name, $description, $content, $status, $order_column, $position )
{
    global $reg_errors, $wpdb;

    if ( 1 > count( $reg_errors->get_error_messages() ) ) {

        $order_no = $wpdb->query("SELECT LAST_INSERT_ID() FROM {$table_name}");
        $order_no = (int)$order_no + 1;

        $wpdb->query( $wpdb->prepare( 
            "
                INSERT INTO {$table_name}
                ( description, content, status, {$order_column}, position, create_datetime )
                VALUES ( %s, %s, %s, %d, %d, %s )
            ", 
            ucwords( $description ), 
            $content, 
            $status,
            $order_no,
            $position,
            current_time( 'mysql' )
        ) );
        
        redirect_to_dashboard();

    }

}