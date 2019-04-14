<?php
/**
 * @package  Header and Body Tag Injection Plugin
 */
namespace Inc\Base;

class CreateTables
{
    public $header_table;
    public $body_table;
	public $charset_collate;

	function __construct()
	{	
		global $wpdb;
		$this->charset_collate = $wpdb->get_charset_collate();
        $this->header_table = $wpdb->prefix . 'header_data_plugin';
        $this->body_table = $wpdb->prefix . 'body_data_plugin';
	}

	public function createHeaderTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS $this->header_table (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		description longtext DEFAULT NULL,
		content longtext DEFAULT NULL,
		status varchar(10) DEFAULT 'Active' NOT NULL,
		header_order mediumint(9) DEFAULT 1 NOT NULL,
		position int(1) DEFAULT 1 NOT NULL,
		create_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		update_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
		) $this->charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
    }
    
    public function createBodyTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS $this->body_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			description longtext DEFAULT NULL,
			content longtext DEFAULT NULL,
			status varchar(10) DEFAULT 'Active' NOT NULL,
			body_order mediumint(9) DEFAULT 1 NOT NULL,
			position int(1) DEFAULT 1 NOT NULL,
			create_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			update_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
			) $this->charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function sampleHeaderData()
	{      
        global $wpdb;

        // INSERT DUMP DATA TO header_data_plugin table		
		$description	= "Google Analytics Tag";
		$content = htmlspecialchars("<!-- Google Analytics -->
		<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		
		ga('create', 'UA-XXXXX-Y', 'auto');
		ga('send', 'pageview');
		</script>
		<!-- End Google Analytics -->");
		$status = 'Active';

		$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $this->header_table
				( description, content, status, header_order, position, create_datetime )
				VALUES ( %s, %s, %s, %d, %d, %s )
			", 
			$description, 
			$content, 
			$status,
			1,
			1,
			current_time( 'mysql' )
		) );
        
	}

	public function sampleBodyData()
	{      
        global $wpdb;

        // INSERT DUMP DATA TO body_data_plugin table		
		$description	= "Google Analytics Body Tag";
		$content = htmlspecialchars("<p>Sample Paragraph</p>");
		$status = 'Active';

		$wpdb->query( $wpdb->prepare( 
			"
				INSERT INTO $this->body_table
				( description, content, status, body_order, position, create_datetime )
				VALUES ( %s, %s, %s, %d, %d, %s )
			", 
			$description, 
			$content, 
			$status,
			1,
			1,
			current_time( 'mysql' )
		) );
        
	}
}