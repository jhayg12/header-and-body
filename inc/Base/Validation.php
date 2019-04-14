<?php

$plugin_path = plugin_dir_path( dirname( __FILE__, 3 ) );

require_once( $plugin_path . "header-and-body/inc/Base/Validation/FormValidation.php" );

if ( $_POST[ 'submit' ] ) {

    global $wpdb;
    $tagName = $_POST['tagName'];
    $tagInitials = ( $tagName === 'header' ) ? 'h' : 'b';    
    $table_name = $wpdb->prefix . $tagName . '_data_plugin';
    
    // Validation 
    form_validation( $_POST[ 'desc' ], $_POST[ 'content' ], $_POST[ $tagInitials . '-codemirror-error' ] );

    global $description, $content, $status;
    $description = esc_attr( $_POST[ 'desc' ] );
    $content = esc_textarea( $_POST[ 'content' ] );
    $status = esc_attr( ( ! empty( $_POST[ 'status' ] ) ? 'Active' : 'Inactive' ) );
    $order_column = $tagName . '_order';
    $position = esc_attr( ( ! isset( $_POST[ 'position' ] ) ? 1 : 0 ) );
    $tagAction = ( isset( $_POST[ 'tagAction' ] ) ? $_POST[ 'tagAction' ] : '' );
    $tagId = esc_attr( $_POST[ $tagInitials . 'id' ] );
    
    if ( ! empty ( $tagAction ) ) {

        // Data Insertion
        if ( $tagAction === 'Add' ) {
            require_once( $plugin_path . "header-and-body/inc/Base/Models/InsertData.php" );
            insert_to_database( $table_name, $description, $content, $status, $order_column, $position );
        }

        // Data Update
        if ( $tagAction === 'Edit' ) {
            require_once( $plugin_path . "header-and-body/inc/Base/Models/UpdateData.php" );
            update_database_record( $table_name, $description, $content, $status, $position, $tagId );
        }

    }

	
}
