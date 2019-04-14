<?php 
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Api;

use Inc\Base\TableController;

class BodySettings extends TableController
{
    public $table_name;

    public $total_records;
    public $total_active_records;
    public $total_inactive_records;

    /**************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct() 
    {
        global $status, $page;
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'body_data_plugin'; 
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'body',     //singular name of the listed records
            'plural'    => 'bodies',    //plural name of the listed records
            'ajax'      => false,        //does this table support ajax?
            'tablenav' => 'tablenav-body'
        ) );
        
    }

    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'body-order-up':
            case 'body-order-down':
            case 'body-position':
            case 'body_description':
            case 'body-content':
            case 'body-status':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (head description only)
     **************************************************************************/
    function column_body_description( $item )
    {
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=edit_body&action=%s&bid=%s">Edit</a>','edit',$item['id'] ),
            'view'    => sprintf( '<a href="'. get_site_url() .'/wp-admin/options-general.php?page=view_body&action=%s&bid=%s">View</a>','view',$item['id'] ),
            'delete'    => sprintf( '<a class="delete-body" id="'. $item['id'] .'" href="'. get_site_url() .'/wp-admin/options-general.php?page=delete_body&action=%s&bid=%s">Delete</a>','delete',$item['id'] ),
        );
        
        //Return the title contents
        // return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
        //     /*$1%s*/ $item['description'],
        //     /*$2%s*/ $item['id'],
        //     /*$3%s*/ $this->row_actions($actions)
        // );

        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['body_description'],
            /*$2%s*/ $this->row_actions( $actions )
        );
    }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (head description only)
     **************************************************************************/
    function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" class="body-check"/>',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("record")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }

    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'body-order-up' => '',
            'body-order-down' => '',
            'body-position' => '',
            'body_description'     => 'Description',
            'body-content'    => 'Content',
            'body-status'  => 'Status',
        );
        return $columns;
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'body_description'     => array('body_description',false),     //true means it's already sorted
            'body-content'    => array('body-content',false),
            'body-status'  => array('body-status',false),
        );
        return $sortable_columns;
    }

    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() 
    {
        $actions = array(
            'activate_body' => 'Activate',
            'deactivate_body' => 'Deactivate',
            'delete_body' => 'Delete'
        );
        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() 
    {
        
        global $wpdb;

        // Detect when delete bulk action is being triggered...
        // if( 'delete' === $this->current_action() ) {
        //     $body_data;

        //     if ( ! empty ( $_GET[ 'body' ] ) ) {
        //         $body_data = $_GET[ 'body' ];

        //         for ( $x = 0; $x <= count( $body_data ); $x++ ) {
        //             $hid = $body_data[$x];
        //             $wpdb->query("DELETE FROM {$this->table_name} WHERE id = $hid");
        //         }
        //     }
            
        // }

        // Detect when activate bulk action is being triggered...
        if( 'activate_body' === $this->current_action() ) {
            $body_data;

            if ( ! empty ( $_GET[ 'body' ] ) ) {
                $body_data = $_GET[ 'body' ];

                for ( $x = 0; $x <= count( $body_data ); $x++ ) {
                    $hid = $body_data[$x];
                    $wpdb->query("UPDATE {$this->table_name} SET Status = 'Active' WHERE id = $hid");
                }
            }
        }

        // Detect when deactivate bulk action is being triggered...
        if( 'deactivate_body' === $this->current_action() ) {
            $body_data;

            if ( ! empty ( $_GET[ 'body' ] ) ) {
                $body_data = $_GET[ 'body' ];

                for ( $x = 0; $x <= count( $body_data ); $x++ ) {
                    $hid = $body_data[$x];
                    $wpdb->query("UPDATE {$this->table_name} SET Status = 'Inactive' WHERE id = $hid");
                }
            }
        }
        
    }

    /**
    * This checks for sorting input and sorts the data in our array accordingly.
    * 
    * In a real-world situation involving a database, you would probably want 
    * to handle sorting by passing the 'orderby' and 'order' values directly 
    * to a custom query. The returned data will be pre-sorted, and this array
    * sorting technique would be unnecessary.
    */
    function usort_reorder( $a,$b )
    {
        $orderby = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
        $order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
        $result = strcmp( $a[$orderby], $b[$orderby] ); //Determine sort order
        return ( $order==='asc' ) ? $result : -$result; //Send final sort direction to usort
    }

    /**
     * Get Total Number of Records in the Database
     */
    function get_total_no_of_records()
    {
        global $wpdb;
        $total_records = $wpdb->get_results( "SELECT COUNT(*) FROM {$this->table_name}" );
        $total_records = json_decode( json_encode($total_records), true );
        return ( is_null( $total_records[0]['COUNT(*)'] ) ) ? '0' : $total_records[0]['COUNT(*)'];
    }

    /**
     * Get Total Number of Active Records in the Database
     */
    function get_total_no_of_active_records()
    {
        global $wpdb;
        $total_records = $wpdb->get_results( "SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'Active' " );
        $total_records = json_decode( json_encode($total_records), true );
        return ( is_null( $total_records[0]['COUNT(*)'] ) ) ? '0' : $total_records[0]['COUNT(*)'];
    }

    /**
     * Get Total Number of Deactive Records in the Database
     */
    function get_total_no_of_deactive_records()
    {
        global $wpdb;
        $total_records = $wpdb->get_results( "SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'Inactive' " );
        $total_records = json_decode( json_encode($total_records), true );
        return ( is_null( $total_records[0]['COUNT(*)'] ) ) ? '0' : $total_records[0]['COUNT(*)'];
    }

    /**
    * Retrieve data from the database
    */
    function retrieve_data_from_database()
    {
        global $wpdb;
        $filter_by = '';
        $arr_data = array();

        if ( isset( $_GET[ 'action' ] ) || ! empty( $_GET[ 'action' ] ) ) {

            if ( $_GET[ 'action' ] === 'head' ) {

                if ( isset( $_GET[ 'filter' ] ) || ! empty( $_GET[ 'filter' ] ) ) {
                    
                    $request_filter = $_GET[ 'filter' ];
                    
                    $filter_by = ( $request_filter === 'active' ) ? 'Active' : 'Inactive';
        
                    $obj_data = $wpdb->get_results( "SELECT id, description, content, status, body_order, position FROM {$this->table_name} WHERE Status = '{$filter_by}' ORDER BY position ASC, body_order ASC" );
                    $obj_temp = json_decode( json_encode( $obj_data ), true );
                    for ( $x = 0; $x < count( $obj_temp ); $x++ ) {

                        $arr_temp = array(
                                    'id' => $obj_temp[$x]['id'],
                                    'body-order-up' => '<i title="Move Up" class="body-ord-up" id="'. $obj_temp[$x]['id'] .'">&#9650;</i>',
                                    'body-order-down' => '<i title="Move Down" class="body-ord-down" id="'. $obj_temp[$x]['id'] .'">&#9660;</i>',
                                    'body_description' => $obj_temp[$x]['description'],
                                    'body-content' => $obj_temp[$x]['content'],
                                    'body-status' => $obj_temp[$x]['status'],
                                );

                        // Position 0 is TOP and 1 is BOTTOM
                        if ( $obj_temp[$x]['position'] == 0 ) {
                            $arr_temp['body-position'] = '<label class="toggle-check">
                                                                    <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="body-toggle-check-input" checked/>
                                                                    <span class="toggle-check-text"></span>
                                                                </label>';
                        } else {
                            $arr_temp['body-position'] = '<label class="toggle-check">
                                                                    <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="body-toggle-check-input"/>
                                                                    <span class="toggle-check-text"></span>
                                                                </label>';
                        }

                        array_push( $arr_data, $arr_temp );
                    }

                    return $arr_data;
        
                }
            }
        }

        $obj_data = $wpdb->get_results( "SELECT id, description, content, status, body_order, position FROM {$this->table_name} ORDER BY position ASC, body_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
        for ( $x = 0; $x < count( $obj_temp ); $x++ ) {

            $arr_temp = array(
                        'id' => $obj_temp[$x]['id'],
                        'body-order-up' => '<i title="Move Up" class="body-ord-up" id="'. $obj_temp[$x]['id'] .'">&#9650;</i>',
                        'body-order-down' => '<i title="Move Down" class="body-ord-down" id="'. $obj_temp[$x]['id'] .'">&#9660;</i>',
                        'body_description' => $obj_temp[$x]['description'],
                        'body-content' => $obj_temp[$x]['content'],
                        'body-status' => $obj_temp[$x]['status'],
                    );
            
            // Position 0 is TOP and 1 is BOTTOM
            if ( $obj_temp[$x]['position'] == 0 ) {
                $arr_temp['body-position'] = '<label class="toggle-check">
                                                        <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="body-toggle-check-input" checked/>
                                                        <span class="toggle-check-text"></span>
                                                    </label>';
            } else {
                $arr_temp['body-position'] = '<label class="toggle-check">
                                                        <input type="checkbox" id="'. $obj_temp[$x]['id'] .'" class="body-toggle-check-input"/>
                                                        <span class="toggle-check-text"></span>
                                                    </label>';
            }

            array_push( $arr_data, $arr_temp );
        }

        return $arr_data;
    }

    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() 
    {

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        $data = $this->retrieve_data_from_database();
                
        usort( $data, array($this, 'usort_reorder') );
                        
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,( ( $current_page-1 ) * $per_page ), $per_page );
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
        ) );
    }

}