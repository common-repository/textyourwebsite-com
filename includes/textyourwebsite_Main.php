<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $wpdb;

$tblname = $wpdb->prefix.'textyourwebsite_pl';
$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");

if(count($result) > 0){

	$clientApiId = $result[0]->client_api_id;

	$shortcode = $result[0]->shortcode;

	if($shortcode == 0) {
		
		add_action('wp_footer', 'text_my_websiteinsert_my_footer');

		function text_my_websiteinsert_my_footer() {

			global $wpdb;

			$tblname = $wpdb->prefix.'textyourwebsite_pl';
			$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");

			if(count($result) > 0){

				$clientApiId = $result[0]->client_api_id;
				
				wp_print_script_tag(
					 array(
						 'id'        => 'textyourwebsiteidclienttag',
						 'data-client' => esc_html("$clientApiId"),
						 'src'       => esc_url( 'https://textyourwebsite.com/app.js' ),
					 )
				 );
				
			}
		}
		
		add_shortcode( 'textyourwebsite', 'textyourwebsite_boxed' );

		function textyourwebsite_boxed( $atts, $content = null, $tag = '' ) {
			$output = '';
		 	return $output;
		}
		
	} else {
		
		add_shortcode( 'textyourwebsite', 'textyourwebsite_boxed' );

		function textyourwebsite_boxed( $atts, $content = null, $tag = '' ) {
			
			global $wpdb;

			$tblname = $wpdb->prefix.'textyourwebsite_pl';
			$result = $wpdb->get_results( "SELECT * FROM $tblname ORDER BY id ASC LIMIT 1 ");

			if(count($result) > 0){

				$clientApiId = $result[0]->client_api_id;
			
				$response = wp_remote_get( 'https://www.textyourwebsite.com/api/'. $clientApiId );
				return $response['body'];
			}
			
		}
		
	}
}


if( !class_exists('textyourwebsite_Main') ){

	/**
	 * Plugin Main Class
	 */
	class textyourwebsite_Main
	{
		public $plugin_file;
		public $plugin_dir;
		public $plugin_path;
		public $plugin_url;
	
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
			$this->plugin_file = textyourwebsite_PLUGIN_FILE;
			$this->plugin_path = trailingslashit( dirname( $this->plugin_file ) );
			$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
			$this->plugin_url  = str_replace( basename( $this->plugin_file ), '', plugins_url( basename( $this->plugin_file ), $this->plugin_file ) );

			add_action('plugins_loaded', array( $this, 'plugins_loaded' ), 1);
			add_filter( 'plugin_action_links', array($this,'fn_add_settings_link_plugin'), 10, 4 );
			add_filter( 'network_admin_plugin_action_links', array($this,'fn_add_settings_link_plugin'), 10, 4 );
			add_action('admin_menu', array($this,'fn_textyourwebsite_admin_menu_callback'));
			add_action('admin_enqueue_scripts', array($this, 'fn_textyourwebsite_enqueue_admin_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'fn_textyourwebsite_enqueue_front_scripts'));
			add_action('wp_ajax_submit_verifyKeysform', array($this,'fn_textyourwebsite_submit_verifyKeysform'));
		}
		
		/**
		 * plugin activation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function activate() {
			$plugin_path = dirname( textyourwebsite_PLUGIN_FILE );

			require_once $plugin_path . '/includes/textyourwebsite-db-config.php';
			$db_obj = new textyourwebsite_DB_Config();
			$db_obj->fn_create_textyourwebsite_tables();
		}

		/**
		 * plugin deactivation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function deactivate( $network_deactivating ) {

		}
		
		/**
		 * plugin deactivation callback
		 * @see register_uninstall_hook()
		 *
		 * @param bool $network_uninstalling
		 */
		public static function uninstall() {
		   
		    global $table_prefix, $wpdb;
		    
		    $plugin_path = dirname( textyourwebsite_PLUGIN_FILE );
		    require_once $plugin_path . '/includes/textyourwebsite-db-config.php';
			$db_obj = new textyourwebsite_DB_Config();
			$tblname = $db_obj->textyourwebsite_pl_table;
			$textyourwebsite_pl_table = $table_prefix . "$tblname";
			$wpdb->query( "DROP TABLE IF EXISTS $textyourwebsite_pl_table" );
		}
		
		public function plugins_loaded() {
			$this->loadLibraries();
		}

		/**
		 * Load all the required library files.
		 */
		protected function loadLibraries() {

			require_once $this->plugin_path . 'includes/textyourwebsite-db-config.php';
		}

		public function fn_add_settings_link_plugin( $actions, $plugin_file, $plugin_data, $context ) {
 
		    // Add settings action link for plugins
		    if ( !array_key_exists( 'settings', $actions ) && $plugin_file == "WordpressPlugin-main/textyourwebsite.php" && current_user_can( 'manage_options' ) ){

		    	$url = admin_url( "admin.php?page=textyourwebsite" );
		    	$actions['settings'] = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'textyourwebsite' ) );
		    }
		    
		    return $actions;
		}

		public function fn_textyourwebsite_admin_menu_callback(){

			add_menu_page(
		        __( 'textyourwebsite', 'textyourwebsite' ),
		        'TextYourWebsite',
		        'manage_options',
		        'textyourwebsite',
		        array($this,'fn_textyourwebsite_admin_menu_page_callback'),
		        'dashicons-format-status',
		        25
		    );
		}

		function fn_textyourwebsite_admin_menu_page_callback(){

			global $table_prefix, $wpdb;
			$db_obj = new textyourwebsite_DB_Config();
			$tblname = $db_obj->textyourwebsite_pl_table;
			$textyourwebsite_pl_table = $table_prefix . "$tblname";
			$result = $wpdb->get_results( "SELECT * FROM $textyourwebsite_pl_table ORDER BY id ASC LIMIT 1 ");

			$record_id = $client_api_id = $shortcode = "";
			$shortcode_sec_class = "shortcode_sec_class_hide"; 
			if(count($result) > 0){
				$shortcode_sec_class = "shortcode_sec_class_show";
				$record_id = $result[0]->id;	
				$client_api_id = $result[0]->client_api_id;	
				$shortcode = $result[0]->shortcode;	
			}
			
			require_once( $this->plugin_path. 'includes/view/admin/textyourwebsite-auth.php');
		}
        
        public function fn_textyourwebsite_enqueue_admin_scripts(){

			wp_enqueue_style( 'textyourwebsite-admin-css', $this->plugin_url."assets/css/admin.css" );
			wp_enqueue_script( 'textyourwebsite-admin-script', $this->plugin_url."assets/js/admin.js" );

			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php'),
			);
			wp_localize_script( 'textyourwebsite-admin-script', 'script_params', $params );
		}

		public function fn_textyourwebsite_enqueue_front_scripts(){

			wp_enqueue_style( 'textyourwebsite-front-css', $this->plugin_url."assets/css/front_style.css" );
		}
		
		public function fn_textyourwebsite_submit_verifyKeysform() {

			$record_id = sanitize_text_field( $_POST['record_id'] );
			$client_api_id = sanitize_text_field( $_POST['client_api_id'] );
			$shortcode = sanitize_text_field( $_POST['shortcode'] );
			$response = array('status' => 'failed', 'msg' => 'Something went wrong, please try again after some time.');
			
			if(empty($client_api_id)){
				echo json_encode($response);
				exit();
			}

			global $wpdb;
			$db_obj = new textyourwebsite_DB_Config();
			$tblname = $db_obj->textyourwebsite_pl_table;

			if(!empty($record_id) && $record_id > 0 ){

				$updated = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}$tblname SET client_api_id='$client_api_id', shortcode='$shortcode' WHERE id = $record_id"));
				
				if( $updated !== false ){

					wp_send_json_success( array( 'message' => __( 'Details updated successfully.', 'textyourwebsite' ) ) );
				}else if( $updated == 0 ){
					wp_send_json_error( array( 'message' => __( 'Please enter different values to update the record.', 'textyourwebsite' ) ) );
				}
				
			}else{

				$inserted = $wpdb->query("INSERT INTO {$wpdb->prefix}$tblname (client_api_id,shortcode) VALUES ('$client_api_id','$shortcode')"  );
				if( $inserted !== false ){
					wp_send_json_success( array( 'message' => __( 'Details saved successfully.', 'textyourwebsite' ) ) );
				}
			}
			
			wp_send_json_error( array('message' => 'Something went wrong, please try again after some time.') );
		}
    }
}
