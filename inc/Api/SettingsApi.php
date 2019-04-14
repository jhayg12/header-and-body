<?php 
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Api;

use Inc\Base\HeaderSettings;

class SettingsApi
{

    public $fields = array();
    public $settings = array();
    public $sections = array();
    public $admin_subpages = array();
    
    /**
     * Register Pages
     */
    public function register()
    {
        if ( ! empty( $this->admin_subpages ) ) {
            add_action( 'admin_menu', array( $this, 'addSubMenu' ) );
            add_filter( 'submenu_file', array( $this, 'subMenuFilter' ) );
        }

        if ( ! empty( $this->settings ) ) {
            add_action( 'admin_init', array( $this, 'registerCustomFields' ) );
        }

    }

    /**
     * Gets the subpages from Admin::class
     */
    public function addSubPage( array $pages )
    {   
        $this->admin_subpages = $pages;
        return $this;
    }

    /**
     * Loop through the submenus
     */
    public function addSubMenu()
    {
        foreach ( $this->admin_subpages as $page ) {
            add_submenu_page( $page[ 'parent_slug' ], $page[ 'page_title' ], $page[ 'menu_title' ], $page[ 'capability' ], $page[ 'menu_slug' ], $page[ 'callback' ] );
            remove_submenu_page('options-general.php?page=add_header','add_header');
        }
    }

    /**
     * Filters submenus to hide add/edit/view tags
     * @return Array
     */
    public function subMenuFilter( $submenu_file ) {

        global $plugin_page;
    
        $hidden_submenus = array(
            'add_header' => true,
            'add_body' => true,
            'edit_header' => true,
            'edit_body' => true,
            'view_header' => true,
            'view_body' => true,
            'delete_header' => true,
            'delete_body' => true,
        );

        // Select another submenu item to highlight (optional).
        if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
            $submenu_file = 'header_body';
        }
        
        // Hide the submenu.
        foreach ( $hidden_submenus as $submenu => $unused ) {
            remove_submenu_page( 'options-general.php', $submenu );
        }
    
        return $submenu_file;
    }


}