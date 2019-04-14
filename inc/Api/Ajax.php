<?php

namespace Inc\Api;

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

class Ajax
{

    public $header_table;
    public $body_table;

    public function __construct()
    {
        global $wpdb;
        $this->header_table = $wpdb->prefix . 'header_data_plugin';
        $this->body_table = $wpdb->prefix . 'body_data_plugin';

        $this->delete();
        $this->position();
        $this->orderUp();
        $this->orderDown();

        $this->get_all_records();
        $this->get_active_records();
        $this->get_inactive_records();
    }

    /**
     * Handles the deletion of records
     *
     * @return void
     */
    public function delete()
    {   
        global $wpdb;
        
        // Header
        if ( ! empty ( $_POST[ 'hid' ] ) ) {
            $hid = $_POST[ 'hid' ];
            $wpdb->query(" DELETE FROM {$this->header_table} WHERE id = {$hid} ");
            $this->header_output();
        }

        // Body
        if ( ! empty ( $_POST[ 'bid' ] ) ) {
            $bid = $_POST[ 'bid' ];
            $wpdb->query(" DELETE FROM {$this->body_table} WHERE id = {$bid} ");
        }
    }

    /**
     * Handles the positioning of records
     *
     * @return void
     */
    public function position()
    {   
        global $wpdb;

        // Header
        if ( ! empty ( $_POST[ 'hPosId' ] ) ) {
            $hPosId = $_POST[ 'hPosId' ];
            $obj_data = $wpdb->get_results(" SELECT position FROM {$this->header_table} WHERE id = {$hPosId} LIMIT 1");
            $obj_temp = json_decode( json_encode( $obj_data ), true );

            if ( $obj_temp[0]['position'] == 0 ) {
                $wpdb->query(" UPDATE {$this->header_table} SET position = 1 WHERE id = {$hPosId} ");
            } else {
                $wpdb->query(" UPDATE {$this->header_table} SET position = 0 WHERE id = {$hPosId} ");
            }

            $this->header_output();

        }

        // Body
        if ( ! empty ( $_POST[ 'bPosId' ] ) ) {
            $bPosId = $_POST[ 'bPosId' ];
            $obj_data = $wpdb->get_results(" SELECT position FROM {$this->body_table} WHERE id = {$bPosId} LIMIT 1");
            $obj_temp = json_decode( json_encode( $obj_data ), true );

            if ( $obj_temp[0]['position'] == 0 ) {
                $wpdb->query(" UPDATE {$this->body_table} SET position = 1 WHERE id = {$bPosId} ");
            } else {
                $wpdb->query(" UPDATE {$this->body_table} SET position = 0 WHERE id = {$bPosId} ");
            }

            $this->body_output();

        }

    }

    /**
     * Handles the Order Up
     *
     * @return void
     */
    public function orderUp()
    {   
        global $wpdb;
        
        // Header
        if ( ! empty ( $_POST[ 'hUpId' ] ) ) {
            $hUpId = $_POST[ 'hUpId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP header_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->header_table} WHERE position = 0 ORDER BY header_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['header_order'] );
            }

            // Get all the TOP header_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->header_table} WHERE position = 1 ORDER BY header_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['header_order'] );
            }

            // Get the original header_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, header_order FROM {$this->header_table} WHERE id = {$hUpId} LIMIT 1");
            $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
            
            // Check if the selected record is in TOP position then process
            if ( $orig_obj_temp[0]['position'] == 0) {

                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $topIds );
                $orig_value = $topIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $topIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->header_table} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $temp_value WHERE id = {$hUpId} ");
                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            } else {

                // Process if position is Bottom
                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $bottomIds );
                $orig_value = $bottomIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $bottomIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->header_table} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $temp_value WHERE id = {$hUpId} ");
                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->header_output();

        }


        // Body
        if ( ! empty ( $_POST[ 'bUpId' ] ) ) {
            $bUpId = $_POST[ 'bUpId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP body_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT body_order FROM {$this->body_table} WHERE position = 0 ORDER BY body_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['body_order'] );
            }

            // Get all the TOP body_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT body_order FROM {$this->body_table} WHERE position = 1 ORDER BY body_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['body_order'] );
            }

            // Get the original body_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, body_order FROM {$this->body_table} WHERE id = {$bUpId} LIMIT 1");
            $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
            
            // Check if the selected record is in TOP position then process
            if ( $orig_obj_temp[0]['position'] == 0) {

                $orig_key = array_search( $orig_obj_temp[0]['body_order'], $topIds );
                $orig_value = $topIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $topIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->body_table} WHERE body_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $temp_value WHERE id = {$bUpId} ");
                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $orig_value WHERE id = {$temp_id} ");
                }

            } else {

                // Process if position is Bottom
                $orig_key = array_search( $orig_obj_temp[0]['body_order'], $bottomIds );
                $orig_value = $bottomIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $bottomIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->body_table} WHERE body_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $temp_value WHERE id = {$bUpId} ");
                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->body_output();

        }


    }

    /**
     * Handles the Order Down
     *
     * @return void
     */
    public function orderDown()
    {   
        global $wpdb;
        
        // Header
        if ( ! empty ( $_POST[ 'hDownId' ] ) ) {
            $hDownId = $_POST[ 'hDownId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP header_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->header_table} WHERE position = 0 ORDER BY header_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['header_order'] );
            }

            // Get all the TOP header_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->header_table} WHERE position = 1 ORDER BY header_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['header_order'] );
            }

            // Get the original header_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, header_order FROM {$this->header_table} WHERE id = {$hDownId} LIMIT 1");
            $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
            
            // Check if the selected record is in TOP position then process
            if ( $orig_obj_temp[0]['position'] == 0) {

                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $topIds );
                $orig_value = $topIds[ $orig_key ];

                $keys = array_keys( $topIds );
                $last_key = end( $keys );

                if ( $orig_key < $last_key ) {
                    $temp_key = (int)$orig_key + 1; 
                    $temp_value = $topIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->header_table} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $temp_value WHERE id = {$hDownId} ");
                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            } else {

                // Process if position is Bottom
                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $bottomIds );
                $orig_value = $bottomIds[ $orig_key ];

                $keys = array_keys( $bottomIds );
                $last_key = end( $keys );

                if ( $orig_key < $last_key ) {
                    $temp_key = (int)$orig_key + 1; 
                    $temp_value = $bottomIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->header_table} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $temp_value WHERE id = {$hDownId} ");
                    $wpdb->get_results(" UPDATE {$this->header_table} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->header_output();

        }


        // Body
        if ( ! empty ( $_POST[ 'bDownId' ] ) ) {
            $bDownId = $_POST[ 'bDownId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP body_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT body_order FROM {$this->body_table} WHERE position = 0 ORDER BY body_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['body_order'] );
            }

            // Get all the TOP body_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT body_order FROM {$this->body_table} WHERE position = 1 ORDER BY body_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['body_order'] );
            }

            // Get the original body_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, body_order FROM {$this->body_table} WHERE id = {$bDownId} LIMIT 1");
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
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->body_table} WHERE body_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $temp_value WHERE id = {$bDownId} ");
                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $orig_value WHERE id = {$temp_id} ");
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
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->body_table} WHERE body_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $temp_value WHERE id = {$bDownId} ");
                    $wpdb->get_results(" UPDATE {$this->body_table} SET body_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->body_output();

        }

    }

    /**
     * Get All Records
     *
     * @return Array
     */
    public function get_all_records()
    {
        if ( $_POST['hAll'] ) {
            $this->header_output( 'ALL' );
            $this->header_output();
        }

        if ( $_POST['bAll'] ) {
            $this->body_output( 'ALL' );
            $this->body_output();
        }
    }

    /**
     * Get Active Records
     *
     * @return Array
     */
    public function get_active_records()
    {
        if ( $_POST['hActive'] ) {
            $this->header_output( 'ACTIVE' );
        }

        if ( $_POST['bActive'] ) {
            $this->body_output( 'ACTIVE' );
        }
    }

    /**
     * Get Inactive Records
     *
     * @return Array
     */
    public function get_inactive_records()
    {
        if ( $_POST['hInactive'] ) {
            $this->header_output( 'INACTIVE' );
        }

        if ( $_POST['bInactive'] ) {
            $this->body_output( 'INACTIVE' );
        }
    }

    /**
     * Renders the Header Updated Table Rows
     *
     * @return Array
     */
    public function header_output( string $record_type = null )
    {
        global $wpdb;
        $arr_data = array();

        if ( $record_type === 'ALL' || $record_type == NULL ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, header_order, position FROM {$this->header_table} ORDER BY position ASC, header_order ASC" );
        }

        if ( $record_type === 'ACTIVE' ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, header_order, position FROM {$this->header_table} WHERE Status = 'Active' ORDER BY position ASC, header_order ASC" );
        }

        if ( $record_type === 'INACTIVE' ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, header_order, position FROM {$this->header_table} WHERE Status = 'Inactive' ORDER BY position ASC, header_order ASC" );
        }

        $obj_temp = json_decode( json_encode( $obj_data ), true );

        if ( count( $obj_temp ) > 0 ) {
            for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
   
                $isChecked = ( $obj_temp[$x]['position'] === '0' ) ? 'checked' : '';
    
                array_push( $arr_data, '<tr>
                                            <th scope="row" class="check-column"><input type="checkbox" name="header[]" value="'. $obj_temp[$x]['id'] .'" class="header-check"></th>
                                            <td class="header-order-up column-header-order-up has-row-actions column-primary" data-colname=""><i title="Move Up"
                                                    class="header-ord-up" id="'. $obj_temp[$x]['id'] .'">&#9650;</i><button type="button" class="toggle-row"><span class="screen-reader-text">Show
                                                        more details</span></button></td>
                                            <td class="header-order-down column-header-order-down" data-colname=""><i title="Move Down" class="header-ord-down"
                                                    id="'. $obj_temp[$x]['id'] .'">&#9660</i></td>
                                            <td class="header-position column-header-position" data-colname=""><label class="toggle-check">
                                                    <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="header-toggle-check-input" '. $isChecked .'>
                                                    <span class="toggle-check-text"></span>
                                                </label></td>
                                            <td class="header_description column-header_description" data-colname="Description">'. $obj_temp[$x]['description'] .' <div class="row-actions"><span
                                                        class="edit">'. sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=edit_header&action=%s&hid=%s">Edit</a>', 'edit', $obj_temp[$x]['id'] ) .'
                                                        | </span><span class="view">'. sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=view_header&action=%s&hid=%s">View</a>', 'view', $obj_temp[$x]['id'] ) .'
                                                        | </span><span class="delete">'. sprintf( '<a class="delete-header" id="'. $obj_temp[$x]['id'] .'" href="'. get_site_url() .'/wp-admin/options-general.php?page=delete_header&action=%s&hid=%s">Delete</a>', 'delete', $obj_temp[$x]['id'] ) .'</span></div><button
                                                    type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
                                            <td class="header-content column-header-content" data-colname="Content">'. $obj_temp[$x]['content'] .'</td>
                                            <td class="header-status column-header-status" data-colname="Status">'. $obj_temp[$x]['status'] .'</td>
                                        </tr>' );
            }
    
            print_r( json_encode( $arr_data ) ); 
            exit();
        }

        $data = '<tr class="no-items"><td class="colspanchange" colspan="7">No items found.</td></tr>';
        print_r( json_encode( $data ) );
        
    }

    /**
     * Renders the Body Updated Table Rows
     *
     * @return Array
     */
    public function body_output( string $record_type = null )
    {
        global $wpdb;
        $arr_data = array();

        if ( $record_type === 'ALL' || $record_type == NULL ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, body_order, position FROM {$this->body_table} ORDER BY position ASC, body_order ASC" );
        }

        if ( $record_type === 'ACTIVE' ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, body_order, position FROM {$this->body_table} WHERE Status = 'Active' ORDER BY position ASC, body_order ASC" );
        }

        if ( $record_type === 'INACTIVE' ) {
            $obj_data = $wpdb->get_results( "SELECT id, description, content, status, body_order, position FROM {$this->body_table} WHERE Status = 'Inactive' ORDER BY position ASC, body_order ASC" );
        }
        $obj_temp = json_decode( json_encode( $obj_data ), true );

        if ( count( $obj_temp ) > 0 ) {
            for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
   
            $isChecked = ( $obj_temp[$x]['position'] === '0' ) ? 'checked' : '';

            array_push( $arr_data, '<tr>
                                        <th scope="row" class="check-column"><input type="checkbox" name="body[]" value="'. $obj_temp[$x]['id'] .'" class="body-check"></th>
                                        <td class="body-order-up column-body-order-up has-row-actions column-primary" data-colname=""><i title="Move Up"
                                                class="body-ord-up" id="'. $obj_temp[$x]['id'] .'">&#9650;</i><button type="button" class="toggle-row"><span class="screen-reader-text">Show
                                                    more details</span></button></td>
                                        <td class="body-order-down column-body-order-down" data-colname=""><i title="Move Down" class="body-ord-down"
                                                id="'. $obj_temp[$x]['id'] .'">&#9660</i></td>
                                        <td class="body-position column-body-position" data-colname=""><label class="toggle-check">
                                                <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="body-toggle-check-input" '. $isChecked .'>
                                                <span class="toggle-check-text"></span>
                                            </label></td>
                                        <td class="body_description column-body_description" data-colname="Description">'. $obj_temp[$x]['description'] .' <div class="row-actions"><span
                                                    class="edit">'. sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=edit_body&action=%s&bid=%s">Edit</a>', 'edit', $obj_temp[$x]['id'] ) .'
                                                    | </span><span class="view">'. sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=view_body&action=%s&bid=%s">View</a>', 'view', $obj_temp[$x]['id'] ) .'
                                                    | </span><span class="delete">'. sprintf( '<a class="delete-body" id="'. $obj_temp[$x]['id'] .'" href="'. get_site_url() .'/wp-admin/options-general.php?page=delete_body&action=%s&bid=%s">Delete</a>', 'delete', $obj_temp[$x]['id'] ) .'</span></div><button
                                                type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
                                        <td class="body-content column-body-content" data-colname="Content">'. $obj_temp[$x]['content'] .'</td>
                                        <td class="body-status column-body-status" data-colname="Status">'. $obj_temp[$x]['status'] .'</td>
                                    </tr>' );
            }

            print_r( json_encode( $arr_data ) );
            exit();

        }

        $data = '<tr class="no-items"><td class="colspanchange" colspan="7">No items found.</td></tr>';
        print_r( json_encode( $data ) );
        
    }

}



$ajax = new Ajax();






