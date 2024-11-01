<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if( !class_exists('textyourwebsite_DB_Config') ){

	/**
	 * Plugin Database files Class
	 */
	class textyourwebsite_DB_Config
	{
				
		public $textyourwebsite_pl_table;
		/**
		 * Static Singleton Holder
		 * @var self
		 */
		protected static $instance;
		
		/**
		 * Get (and instantiate, if necessary) the instance of the class
		 *
		 * @return self
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		public function __construct()
		{			
			$this->textyourwebsite_pl_table = 'textyourwebsite_pl';
		}

		public function fn_create_textyourwebsite_tables()
		{
			global $table_prefix, $wpdb;
			$tblname = $this->textyourwebsite_pl_table;
			$textyourwebsite_pl_table = $table_prefix . "$tblname";
			if($wpdb->get_var( "show tables like '$textyourwebsite_pl_table'" ) != $textyourwebsite_pl_table) 
			{
				$sql = "CREATE TABLE `". $textyourwebsite_pl_table . "` ( ";
		        $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
		        $sql .= "  `client_api_id` varchar(500) NOT NULL, ";
		        $sql .= "  `shortcode` int(11) NOT NULL, ";
		        $sql .= "  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP, ";
		        $sql .= "  PRIMARY KEY (`id`) "; 
		        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
		        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		        dbDelta($sql);
			}
		}
    }
}

