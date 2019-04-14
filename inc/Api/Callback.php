<?php 
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Api;

use Inc\Base\BaseController;

class Callback extends BaseController
{   

    /**
     * Show Tag Page
     */
    public function showTagPage()
    {   
        return require_once( $this->plugin_path . "/templates/Tag.php" );
    }

}