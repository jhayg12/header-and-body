<?php

$plugin_path = plugin_dir_path( dirname( __FILE__, 4 ) );

require_once( $plugin_path . "header-and-body/inc/Base/Validation/Redirect.php" );

function update_database_record( $table_name, $description, $content, $status, $position, $id )
{
    global $reg_errors, $wpdb;

    if ( 1 > count( $reg_errors->get_error_messages() ) ) {

        $wpdb->query( $wpdb->prepare( 
            "
                UPDATE {$table_name}
                SET description = %s, content = %s, status = %s, position = %d, update_datetime = %s 
                WHERE id = %d
            ", 
            ucwords( $description ), 
            $content, 
            $status,
            $position,
            current_time( 'mysql' ),
            $id
        ) );
        
        redirect_to_dashboard();

    }

}