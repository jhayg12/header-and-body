<?php 
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Pages;

use Inc\Api\Callback;
use Inc\Api\SettingsApi;
use Inc\Api\BodySettings;
use Inc\Api\HeaderSettings;
use Inc\Base\BaseController;

class Main extends BaseController
{
	
	private $settings;
	private $callback;
	private $body_callback;
	private $body_settings;
	private $header_callback;
	private $header_settings;
	private $sub_pages = array();
	
	public function __construct()
	{
		$this->settings = new SettingsApi();
		$this->callback = new Callback();
	}

	/**
	 * Register SubPages, Head and Body tags
	 */
	public function register() 
	{	
		
		$this->setSubpages();
		$this->settings->addSubPage( $this->sub_pages )->register();

		$this->loadHeadTags();
		$this->loadBodyTags();

	}
	
	
	/**
	 * Add Action for Head tags
	 */
	public function loadHeadTags()
	{
		add_action( 'wp_head', array( $this, 'getTopHeadTags' ), PHP_INT_MIN );
		add_action( 'wp_head', array( $this, 'getBottomHeadTags' ), PHP_INT_MAX );
	}
	
	/**
	 * Add Action for Body tags
	 */
	public function loadBodyTags()
	{
		// add_filter( 'body_class', array( $this, 'getTopBodyTags' ), PHP_INT_MAX ); 
		$this->getTopBodyTags();
		add_action( 'wp_footer', array( $this, 'getBottomBodyTags' ), PHP_INT_MAX );
	}

	/**
	 * Callback 
	 * Get the list of top head tags from the database
	 * @return String
	 */
	public function getTopHeadTags()
    {   
        global $wpdb;
        $table_name = $wpdb->prefix . 'header_data_plugin';
        $arr_data = array();

        $obj_data = $wpdb->get_results( "SELECT content FROM {$table_name} WHERE Status = 'Active' AND position = 0 ORDER BY position ASC, header_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
                    
        for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
			echo htmlspecialchars_decode( wp_unslash( $obj_temp[$x]['content'] ) );
        }
        
	}

	/**
	 * Callback 
	 * Get the list of bottom head tags from the database
	 * @return String
	 */
	public function getBottomHeadTags()
    {   
        global $wpdb;
        $table_name = $wpdb->prefix . 'header_data_plugin';
        $arr_data = array();

        $obj_data = $wpdb->get_results( "SELECT content FROM {$table_name} WHERE Status = 'Active' AND position = 1 ORDER BY position ASC, header_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
                    
        for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
			echo htmlspecialchars_decode( wp_unslash( $obj_temp[$x]['content'] ) );
        }
        
	}

	/**
	 * Callback 
	 * Get the list of top body tags from the database
	 * @return String
	 */
	public function getTopBodyTags()
    {   
		global $wpdb;
        $table_name = $wpdb->prefix . 'body_data_plugin';
        $arr_data = array();

        $obj_data = $wpdb->get_results( "SELECT content FROM {$table_name} WHERE Status = 'Active' AND position = 0 ORDER BY position ASC, body_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
                    
		ob_start();
		add_action('shutdown', function() {
			if ( is_admin() ) {
				return;
			}
			$final = '';
			$levels = ob_get_level();
			for ($i = 0; $i < $levels; $i++){
				$final .= ob_get_clean();
			}
			echo apply_filters('final_output', $final);
		}, 0);

		add_filter('final_output', function($output) {  
			if ( is_admin() ) {
				return;
			}       
			$after_body = apply_filters('after_body','');
			$output = preg_replace("/(\<body.*\>)/", "$1".$after_body, $output);
			return $output;
		});

		add_filter('after_body',function($after_body){
			// for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
			// 	$after_body.= htmlspecialchars_decode( stripslashes( $obj_temp[$x]['content'] ) );				
			// }
			$after_body.='sampledata';
			return $after_body;
		});

	}
	
	/**
	 * Callback 
	 * Get the list of bottom body tags from the database
	 * @return String
	 */
	public function getBottomBodyTags()
    {   
        global $wpdb;
        $table_name = $wpdb->prefix . 'body_data_plugin';
        $arr_data = array();

        $obj_data = $wpdb->get_results( "SELECT content FROM {$table_name} WHERE Status = 'Active' AND position = 1 ORDER BY position ASC, body_order ASC" );
        $obj_temp = json_decode( json_encode( $obj_data ), true );
                    
        for ( $x = 0; $x < count( $obj_temp ); $x++ ) {
			echo htmlspecialchars_decode( stripslashes( $obj_temp[$x]['content'] ) );
        }
        
    }

	/**
	 * Sets Subpages
	 */
	public function setSubpages()
	{
		$this->sub_pages = array(
            array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Head and Body',
                'menu_title' => htmlspecialchars( '<head> and </body>' ),
                'capability' => 'manage_options',
                'menu_slug' => 'header_body',
				'callback' => array( $this, 'headerBodySection' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Add Head Tag',
                'menu_title' => 'Add Head Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'add_header',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Add Body Tag',
                'menu_title' => 'Add Body Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'add_body',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Edit Head Tag',
                'menu_title' => 'Edit Head Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'edit_header',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Edit Body Tag',
                'menu_title' => 'Edit Body Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'edit_body',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'View Head Tag',
                'menu_title' => 'View Head Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'view_header',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'View Body Tag',
                'menu_title' => 'View Body Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'view_body',
				'callback' => array( $this->callback, 'showTagPage' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Delete Head Tag',
                'menu_title' => 'Delete Head Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'delete_header',
				'callback' => array( $this, 'headerBodySection' ),
			),
			array(
				'parent_slug' => 'options-general.php',
                'page_title' => 'Delete Body Tag',
                'menu_title' => 'Delete Body Tag',
                'capability' => 'manage_options',
                'menu_slug' => 'delete_body',
				'callback' => array( $this, 'headerBodySection' ),
			),
        );
	}

	/**
	 * Sets all the pages templates
	 */
	public function headerBodySection()
	{	
		$bulk_delete_url = plugins_url('/header-and-body/inc/Api/BulkDelete.php');
		$ajax_url = plugins_url('/header-and-body/inc/Api/Ajax.php');

		// <head> Tag Declarations
		$this->header_settings = new HeaderSettings();
		$this->header_settings->prepare_items();
		$total_header_records = $this->header_settings->get_total_no_of_records();
		$total_header_active = $this->header_settings->get_total_no_of_active_records();
		$total_header_deactivate = $this->header_settings->get_total_no_of_deactive_records();
		$add_header_link = get_site_url() . '/wp-admin/options-general.php?page=add_header';

		// </body> Tag Declarations
		$this->body_settings = new BodySettings();
		$this->body_settings->prepare_items();
		$total_body_records = $this->body_settings->get_total_no_of_records();
		$total_body_active = $this->body_settings->get_total_no_of_active_records();
		$total_body_deactivate = $this->body_settings->get_total_no_of_deactive_records();
		$add_body_link = get_site_url() . '/wp-admin/options-general.php?page=add_body';

		?>

		<div class="wrap" id="dashboard-menu">
			<div class="header">
				<div class="header-text">
					<h1>&lt;<?php echo __('head'); ?>&gt; and &lt;<?php echo __('/body'); ?>&gt;</h1>
					<p><?php echo __('TAG INJECTION'); ?></p>
				</div>
			</div>

			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-1"><?php echo __('Settings'); ?></a></li>
				<li><a href="#tab-2"><?php echo __('F.A.Q'); ?></a></li>
				<li><a href="#tab-3"><?php echo __('Support'); ?></a></li>
			</ul>

			<div class="tab-content">
				<div id="tab-1" class="tab-pane active">
					
					<input type="hidden" id="BulkDeleteUrl" value="<?php echo $bulk_delete_url; ?>">
					<input type="hidden" id="ajaxUrl" value="<?php echo $ajax_url; ?>">

					<!-- <head> Tag Section -->
					<h1 class="wp-heading-inline">&lt;<?php echo __('head'); ?>&gt;</h1>
					<a href="<?php echo $add_header_link; ?>" class="page-title-action"><?php echo __('Add New'); ?></a>

					<hr class="wp-header-end">
					<h2 class="screen-reader-text"><?php echo __('Filter plugins list'); ?></h2>

					<ul class="subsubsub">
						<li class="all"><a href="#" id="header-all" class="current" aria-current="page"><?php echo __('All'); ?> <span class="count">(<?php echo $total_header_records; ?>)</span></a> |</li>
						<li class="active"><a href="#" id="header-active"><?php echo __('Active'); ?> <span class="count">(<?php echo $total_header_active; ?>)</span></a> |</li>
						<li class="inactive"><a href="#" id="header-inactive"><?php echo __('Inactive'); ?> <span class="count">(<?php echo $total_header_deactivate; ?>)</span></a> </li>
					</ul>

					<form method="get" id="headTag">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						<input type="hidden" name="hDelAll" id="hDelAll" value="<?php echo $_REQUEST['hDelAll'] ?>" />
						<!-- <?php $this->header_settings->search_box( 'search', 'search_id' ); ?> -->
						<?php $this->header_settings->display() ?>
					</form>

					<br><br>

					<!-- <body> Tag Section -->
					<h1 class="wp-heading-inline">&lt;<?php echo __('/body'); ?>&gt;</h1>
					<a href="<?php echo $add_body_link; ?>" class="page-title-action"><?php echo __('Add New'); ?></a>

					<hr class="wp-header-end">
					<h2 class="screen-reader-text"><?php echo __('Filter plugins list'); ?></h2>

					<ul class="subsubsub">
						<li class="all"><a href="#" id="body-all" class="current" aria-current="page"><?php echo __('All'); ?> <span class="count">(<?php echo $total_body_records; ?>)</span></a> |</li>
						<li class="active"><a href="#" id="body-active"><?php echo __('Active'); ?> <span class="count">(<?php echo $total_body_active; ?>)</span></a> |</li>
						<li class="inactive"><a href="#" id="body-inactive"><?php echo __('Inactive'); ?> <span class="count">(<?php echo $total_body_deactivate; ?>)</span></a> </li>
					</ul>

					<form method="get" id="bodyTag">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						<input type="hidden" name="bDelAll" id="bDelAll" value="<?php echo $_REQUEST['bDelAll'] ?>" />
						<?php $this->body_settings->display() ?>
					</form>
					
				</div>

				<div id="tab-2" class="tab-pane">
					<h3><?php echo __('F.A.Q'); ?></h3>
				</div>

				<div id="tab-3" class="tab-pane">
					<h3><?php echo __('Support'); ?></h3>
				</div>
			</div>

		</div>
	
		<?php
	}


}