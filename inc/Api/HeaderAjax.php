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

class HeaderAjax
{

    public $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'header_data_plugin';

        $this->delete();
        $this->position();
        $this->orderUp();
        $this->orderDown();
    }

    /**
     * Handles the deletion of records
     *
     * @return void
     */
    public function delete()
    {   
        global $wpdb;
        
        if ( ! empty ( $_POST[ 'hid' ] ) ) {
            $hid = $_POST[ 'hid' ];
            $wpdb->query(" DELETE FROM {$this->table_name} WHERE id = {$hid} ");
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

        if ( ! empty ( $_POST[ 'hPosId' ] ) ) {
            $hPosId = $_POST[ 'hPosId' ];
            $obj_data = $wpdb->get_results(" SELECT position FROM {$this->table_name} WHERE id = {$hPosId} LIMIT 1");
            $obj_temp = json_decode( json_encode( $obj_data ), true );

            if ( $obj_temp[0]['position'] == 0 ) {
                $wpdb->query(" UPDATE {$this->table_name} SET position = 1 WHERE id = {$hPosId} ");
            } else {
                $wpdb->query(" UPDATE {$this->table_name} SET position = 0 WHERE id = {$hPosId} ");
            }

            $this->output();

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
        
        if ( ! empty ( $_POST[ 'hUpId' ] ) ) {
            $hUpId = $_POST[ 'hUpId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP header_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->table_name} WHERE position = 0 ORDER BY header_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['header_order'] );
            }

            // Get all the TOP header_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->table_name} WHERE position = 1 ORDER BY header_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['header_order'] );
            }

            // Get the original header_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, header_order FROM {$this->table_name} WHERE id = {$hUpId} LIMIT 1");
            $orig_obj_temp = json_decode( json_encode( $orig_obj_data ), true );
            
            // Check if the selected record is in TOP position then process
            if ( $orig_obj_temp[0]['position'] == 0) {

                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $topIds );
                $orig_value = $topIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $topIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->table_name} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $temp_value WHERE id = {$hUpId} ");
                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            } else {

                // Process if position is Bottom
                $orig_key = array_search( $orig_obj_temp[0]['header_order'], $bottomIds );
                $orig_value = $bottomIds[ $orig_key ];

                if ( $orig_key > 0 ) {
                    $temp_key = (int)$orig_key - 1; 
                    $temp_value = $bottomIds[ $temp_key ]; 

                    // Get the id of the element above of the selected element
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->table_name} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $temp_value WHERE id = {$hUpId} ");
                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->output();

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
        // Handles Header Top Positioning
        if ( ! empty ( $_POST[ 'hDownId' ] ) ) {
            $hDownId = $_POST[ 'hDownId' ];
            $topIds = array();
            $bottomIds = array();
            
            // Get all the TOP header_order in ASC order just like it was presented on the table
            $top_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->table_name} WHERE position = 0 ORDER BY header_order ASC");
            $top_obj_temp = json_decode( json_encode( $top_obj_data ), true );

            for ( $x = 0; $x < count( $top_obj_temp ); $x++ ) {
                array_push( $topIds, $top_obj_temp[$x]['header_order'] );
            }

            // Get all the TOP header_order in ASC order just like it was presented on the table
            $bottom_obj_data = $wpdb->get_results(" SELECT header_order FROM {$this->table_name} WHERE position = 1 ORDER BY header_order ASC");
            $bottom_obj_temp = json_decode( json_encode( $bottom_obj_data ), true );

            for ( $x = 0; $x < count( $bottom_obj_temp ); $x++ ) {
                array_push( $bottomIds, $bottom_obj_temp[$x]['header_order'] );
            }

            // Get the original header_order value of the selected row and the key of the array $topIds 
            $orig_obj_data = $wpdb->get_results(" SELECT position, header_order FROM {$this->table_name} WHERE id = {$hDownId} LIMIT 1");
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
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->table_name} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $temp_value WHERE id = {$hDownId} ");
                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $orig_value WHERE id = {$temp_id} ");
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
                    $temp_obj_data = $wpdb->get_results(" SELECT id FROM {$this->table_name} WHERE header_order = $temp_value LIMIT 1");
                    $temp_obj_temp = json_decode( json_encode( $temp_obj_data ), true );

                    $temp_id = $temp_obj_temp[0]['id']; 

                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $temp_value WHERE id = {$hDownId} ");
                    $wpdb->get_results(" UPDATE {$this->table_name} SET header_order = $orig_value WHERE id = {$temp_id} ");
                }

            }

            $this->output();

        }
    }

    /**
     * Renders the Updated Table Rows
     *
     * @return Array
     */
    public function output()
    {
        global $wpdb;
        $arr_data = array();

        $obj_data = $wpdb->get_results( "SELECT id, description, content, status, header_order, position FROM {$this->table_name} ORDER BY position ASC, header_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
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
                                                    | </span><span class="delete">'. sprintf( '<a class="delete-header" id="'. $item['id'] .'" href="'. get_site_url() .'/wp-admin/options-general.php?page=delete_header&action=%s&hid=%s">Delete</a>', 'delete', $obj_temp[$x]['id'] ) .'</span></div><button
                                                type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>
                                        <td class="header-content column-header-content" data-colname="Content">'. htmlspecialchars_decode( stripslashes( $obj_temp[$x]['content'] ) ) .'</td>
                                        <td class="header-status column-header-status" data-colname="Status">'. $obj_temp[$x]['status'] .'</td>
                                    </tr>' );
        }

        print_r( json_encode( $arr_data ) ); 
        
    }

}

$ajax = new HeaderAjax();






