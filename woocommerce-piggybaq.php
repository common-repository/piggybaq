<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/*
Plugin Name: PiggybaQ
Plugin URI: http://www.piggybaq.com
Description: PiggybaQ plugin
Author: piggybaQ
Author URI: http://www.riksof.com
Developer: Zeeshan Lalani

Version: 1.0.0
*/

/**
 * Check if WooCommerce is active
 **/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( !class_exists( 'WC_Piggybaq' ) ) {
		
		class WC_Piggybaq {

			public $prefix = 'piggybaq_';
			public $config_tbl;
			public $disabled_tbl;
			public $db_version = '1.0';

			public function __construct() {
				global $wpdb;

				$this->config_tbl = $wpdb->prefix . $this->prefix . 'config';
				$this->disabled_tbl= $wpdb->prefix . $this->prefix . 'disabled';

				// create piggybaq required tables
				register_activation_hook( __FILE__, array( &$this, 'piggybaq_install' ) );

				// deactivate plugin
				register_deactivation_hook( __FILE__, array( &$this, 'piggybaq_deactivation' ) );

				// called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
				
				// called after all plugins have loaded
				add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
				
				// called just before the woocommerce template functions are included
				add_action( 'init', array( &$this, 'init' ), 20 );
				
				// take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
				
				add_action( 'admin_menu', array( &$this, 'admin_menu_link' ) );

			}
			
			/**
			 * Take care of anything that needs woocommerce to be loaded.  
			 * For instance, if you need access to the $woocommerce global
			 */
			public function woocommerce_loaded() {

			}
			
			/**
			 * Take care of anything that needs all plugins to be loaded
			 */
			public function plugins_loaded() {

			}

			public function admin_menu_link () {
				add_menu_page( 'piggybaQ', 'piggybaQ', 'manage_options', 'piggybaq-settings', array( &$this, 'admin_plugin_page' ), plugins_url( 'images/icon20.png', __FILE__ ) );
			}

			public function admin_plugin_page () {
				// get global variables to be used in admin page
				global $user_ID;
				global $wpdb;

				// set table name variable
				$table_name = $this->config_tbl;

				// set api related variables.
				$store = get_site_url();
				$endpoint = $store . '/wc-api/v2';
				$callback = $this->get_url() . '&callback_listner=1';

				// get woocommerce api enabled
				$woocommerce_api_enabled = get_option('woocommerce_api_enabled','no');

				// get API Information
				$woocommerce_api_consumer_key = get_user_meta($user_ID, 'woocommerce_api_consumer_key', true);
				$woocommerce_api_consumer_secret = get_user_meta($user_ID, 'woocommerce_api_consumer_secret', true);
				$woocommerce_api_key_permissions = get_user_meta($user_ID, 'woocommerce_api_key_permissions', true);

				// get Store and Config for piggybaq
			    $config_store_id = $this->get_config('store_id');
				$config_user_id = $this->get_config('user_id');
				$store_user = $this->get_config('store_user');
				
				// get first category of this store
				$first_category = $this->get_first_category();

			    // Register the script like this for a plugin:
				wp_register_script( 'piggybaq-admin-script', plugins_url( '/js/piggybaq_admin.js', __FILE__ ) );
				
				// For either a plugin or a theme, you can then enqueue the script:
				wp_enqueue_script( 'piggybaq-admin-script' );

				// Admin Theme CSS
				wp_enqueue_style( 'piggybaq-admin-style', plugins_url( '/css/piggybaq-admin.css', __FILE__ ) );

				// include core function files
				include( 'inc/piggybaq-functions.php' );

				$default_discount = 0;
				$default_category = 0;
				$default_icon_position = $this->get_config('icon_position');

				// get data from api
				$url = $this->get_service_url() . '/api/getdefaults/' .$config_user_id. '/'.$config_store_id ;
				$response = wp_remote_get( $url, array(
					'sslverify' => false,
				));

				if( is_array($response) ) {
					$body = json_decode($response['body'],true);

					$default_discount = $body['data']['default_discount'];
					$default_category = $body['data']['default_category'];
				}
				
				if ( isset($_GET['action']) ) {
					if ( $_GET['action'] == 'index' ) {
						include( 'views/index.php' );
					} else if ( $_GET['action'] == 'edit' ) {
						include( 'views/edit.php' );
					}

				} else {
					include( 'views/index.php' );
				}

			}

			// get first category of this store
			public function get_first_category() {
				$taxonomy     = 'product_cat';  
				$show_count   = 0;      // 1 for yes, 0 for no
				$pad_counts   = 0;      // 1 for yes, 0 for no  
				$title        = '';  
				$empty        = 0;

				$args = array(
							 'taxonomy'     => $taxonomy,
							 'show_count'   => $show_count,
							 'pad_counts'   => $pad_counts,
							 'title_li'     => $title,
							 'hide_empty'   => $empty
				);
				$all_categories = get_categories( $args );
				$name = null;
				if ( $all_categories[0] ) {
					$name = $all_categories[0]->name;
				}

				return $name;
			}


			public function get_url () {
			    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
			    $host = $_SERVER['HTTP_HOST'];
			    $script = $_SERVER['SCRIPT_NAME'];
			    $params = $_SERVER['QUERY_STRING'];

			    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
			    return $currentUrl;
			}

			public function init() {
				global $wpdb;
				// send api keys if not already sent

				$config_store_id = $this->get_config('store_id');
				$config_user_id = $this->get_config('user_id');
				$is_disable = $this->get_config('is_disable');

				if ( $config_store_id && $config_user_id && $is_disable == 0) {

					// show piggybaq icons with each product
					add_action( 'woocommerce_before_shop_loop_item_title', array( &$this, 'show_piggybaq_icon' ), 10 );
					add_action( 'woocommerce_single_product_image_html', array( &$this, 'show_piggybaq_icon' ), 10 );
					
					// hook to notify piggybaQ server when post (product) get updated
					add_action( 'save_post', array( &$this, 'product_saved' ), 10, 3 );

					//order create hook paypal and credit card
					add_action( 'woocommerce_order_status_processing', array( &$this, 'check_piggybaq' ), 10 , 1 );

					//order create hook COD and Bank Transfer
					add_action( 'woocommerce_order_status_on-hold', array( &$this, 'check_piggybaq' ), 10 , 1 );
					
					add_action( 'wp_footer', array( &$this, 'init_scripts' ) );

					add_action( 'wp_ajax_do_piggybaq', array( &$this, 'do_piggybaq' ) );
					add_action( 'wp_ajax_nopriv_do_piggybaq', array( &$this, 'do_piggybaq' ) );

					// prevent editing & deleting of piggybaq products
					add_filter( 'user_has_cap', array( &$this, 'prevent_post_edit' ), 10, 3 );

					// Show a warning message on Products that PiggybaQ products can not edit/delete
					add_action( 'admin_notices', array(&$this, 'prevent_post_edit_admin_notice') );

					wp_register_style( 'google_fonts', '//fonts.googleapis.com/css?family=Lato:100,300,400,700,900' );
					wp_enqueue_style( 'google_fonts' );					
				}

				if ( !$config_store_id || !$config_user_id ) {
					// http://wordpress.stackexchange.com/questions/33458/embed-wordpress-admin-in-an-iframe
					// disable iframe block for admin panel when setup is not done
					remove_action( 'admin_init', 'send_frame_options_header' );
				}

			}

			// http://codex.wordpress.org/Plugin_API/Filter_Reference/user_has_cap
			public function prevent_post_edit ( $allcaps, $cap, $args ) {

				// Bail out if we're not asking about a post:
				if ( 'edit_post' != $args[0] && 'delete_post' != $args[0] ) {
					return $allcaps;
				}

				// Load the post data:
				$post = get_post( $args[2] );
				if ( $post && $post->post_type == 'product' ) {
					// get products tags if equals to 'piggybaq_product'
					$tags = wp_get_post_terms( $post->ID, 'product_tag' );
					$imported = false;

					foreach ( $tags as $tag ) {
						if ( $tag->slug == "piggybaq_product" ) {
							$imported = true;
							break;
						}
					}

					if ( $imported ) {
						// http://wordpress.stackexchange.com/a/69837
						// return false
						$allcaps[$cap[0]] = FALSE;
					}
				}


				return $allcaps;

			}

			public function prevent_post_edit_admin_notice() {
				global $current_screen;
				if ( $current_screen->base == 'edit' ) {
					echo '<div class="update-nag" ><p style="color:#9F6000">&#9888 Products imported using piggybaQ can only be edited or removed from the piggybaQ admin section.</p></div>';
				}
			}

			public function show_piggybaq_icon( $html ) {

				global $product;

				// To show piggybaQ icon on the product if it is imported and enabled by the admin
				$enabled = $this->get_enabled( $product->id );
				if ( $enabled ) {

					$tags = wp_get_post_terms( $product->id, 'product_tag' );
					$imported = false;
					foreach ( $tags as $tag ) {
						if ( $tag->slug == "piggybaq_product" ) {
							$imported = true;
							break;
						}
					}
					
					$default_icon_position = $this->get_config('icon_position');
					$cssClass = 'do_piggybaq' . $default_icon_position;
					
					if ( $imported ) {
						//echo '<span class="piggybaq_partner '.$cssClass.'"></span>';
						echo '<span class="piggybaq_partner '.$cssClass.'">Partner</span>';
					} else {
						echo '<span class="do_piggybaq '.$cssClass.'" data-piggybaq-product_id="'.$product->id.'" href="#"></span>';
					}

				}
				
				if ( $html != '' ) {
					return $html;
				}

			}

			public function init_scripts() {
				
				$config_store_id = $this->get_config('store_id');

				// Register the script like this for a plugin:
				wp_register_script( 'piggybaq-client-script', plugins_url( '/js/piggybaq.js', __FILE__ ) );
				
				// Localize the script with new data
				$object = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'store_id' => $config_store_id,
					'service_url' => $this->get_service_url()
				);

				wp_localize_script( 'piggybaq-client-script', 'piggybaq_server', $object );

				// For either a plugin or a theme, you can then enqueue the script:
				wp_enqueue_script( 'piggybaq-client-script' );

				wp_enqueue_style( 'piggybaq-client-style', plugins_url( '/css/piggybaq.css', __FILE__ ) );
			}

			public function product_saved ($post_id, $post, $update) {
				// triggered if post type is product
				if ( $post->post_type == 'product' ) {
						$config_store_id = $this->get_config('store_id');
						$config_service_url = $this->get_service_url();
				
					// if triggered because of update and post_status is publish
					if ( $update && $post->post_status == 'publish') {
					
						$response = wp_remote_post( $config_service_url . '/api/updaterequest', array(
							'method' => 'POST',
							'body' => array( 
								'product_id' => $post_id,
								'store_id' =>  $config_store_id
							),
							'sslverify' => false
						));
						if ( is_wp_error( $response ) ) {
							error_log("Error Occured");
							$error_message = $response->get_error_message();
							error_log($error_message);							
						} else {
							error_log("Succefully updated");
						}

					} else if ($update && $post->post_status == 'trash') {

						$response = wp_remote_post( $config_service_url . '/api/updaterequest', array(
							'method' => 'POST',
							'body' => array( 
								'product_id' => $post_id,
								'store_id' =>  $config_store_id,
								'is_delete' => true
							),
							'sslverify' => false
						));
						if ( is_wp_error( $response ) ) {
							error_log("Error Occured");
							$error_message = $response->get_error_message();
							error_log($error_message);							
						} else {
							error_log("Succefully deleted");
						}
					}
				}
			}

			public function get_hello () {
				
				$config_service_url = $this->get_service_url();
				
				$response = wp_remote_get( $config_service_url . '/api/hello',array(
					'sslverify' => false,
				));
				
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					return null;
				} else {
					if ( $response['response']['code'] == 200 ) {
						$body = $response['body'];
						return @json_decode($body,true);
					} else {
						return null;
					}
				}
			}

			public function get_plugin_details () {
				return get_plugin_data( __FILE__, false );
			}

			public function piggybaq_install () {
				global $wpdb;
				
				$table_name = $this->config_tbl;

				$table_disabled = $this->disabled_tbl;

				$sql = "CREATE TABLE $table_name (
					id int(11) NOT NULL AUTO_INCREMENT,
					config_name varchar(64) NOT NULL,
					config_value varchar(255) NOT NULL,
					PRIMARY KEY (id)
				);";

				$sql2 = "CREATE TABLE $table_disabled (
					product_id int(11) NOT NULL
				);";
	
 
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				dbDelta( $sql2 );

				$data = array(
					'config_name' => 'service_ssl_url',
					'config_value' => 'https://piggybaq.com'
				);
				$wpdb->insert( $table_name, $data );

				$data = array(
					'config_name' => 'service_url',
					'config_value' => 'http://piggybaq.com'
				);
				$wpdb->insert( $table_name, $data );

				$data = array(
					'config_name' => 'is_disable',
					'config_value' => '0'
				);
				$wpdb->insert( $table_name, $data );

				// default icon position
				$data = array(
					'config_name' => 'icon_position',
					'config_value' => 'TopRight'
				);
				$wpdb->insert( $table_name, $data );

				$data = array(
					'config_name' => 'default_discount',
					'config_value' => '15'
					);
				$wpdb->insert( $table_name, $data );

				add_option( 'piggybaq_db_version', $this->db_version );
			}

			// Disable/Enable switch piggybaQ and is called in piggybaq-functions.php
			public function piggybaq_disable($post) {

				global $wpdb;
				$table_name = $this->config_tbl;
				$do_disable = '0';
				if( isset($post['is_disabled']) ){
					$do_disable = '1';
				}

				$data = array('config_value' => $do_disable );
				$where = array('config_name' => 'is_disable');
				$wpdb->update($table_name, $data, $where);

			}

			public function piggybaq_deactivation () {
				global $wpdb;

				$table_name = $this->config_tbl;
				$table_disabled = $this->disabled_tbl;
				$sql = "DROP TABLE IF EXISTS $table_name";
				$sql2 = "DROP TABLE IF EXISTS $table_disabled";
 				$wpdb->query($sql);
 				$wpdb->query($sql2);

 				delete_option("piggybaq_db_version");
			}

			public function get_service_url () {
				global $wpdb;

				$table_name = $this->config_tbl;
				return $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = 'service_ssl_url'" );
			}

			public function get_service_url_not_ssl () {
				global $wpdb;

				$table_name = $this->config_tbl;
				return $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = 'service_url'" );
			}

			public function get_config ( $name ) {
				global $wpdb;

				$table_name = $this->config_tbl;
				return $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = '$name'" );
			}

			public function get_products () {
				$config_store_id = $this->get_config('store_id');
				
				$url = $this->get_service_url() . '/api/productlist/' . $config_store_id;

				$response = wp_remote_get( $url, array(
					'sslverify' => false,
				));
				$data = array();
				if( is_array($response) ) {
					$header = $response['headers']; // array of http header lines
					$body = $response['body']; // use the content
					
					$body_data = json_decode($body, true);
					if ( $body_data['status'] && $body_data['status'] == 'OK' ) {
						$data = $body_data['data'];
					}
				}

				if ( is_wp_error($response) ) {
					error_log("WP_GET ERROR: " . print_r($response,true));
				}

				return $data;
			}

			public function get_woo_products () {

				$tag = get_term_by( 'name','piggybaq_product', 'product_tag' );
				
				$loop = get_posts( array( 
					'post_type' => 'product', 
					'tax_query'=>array(
						array(
							'taxonomy' => 'product_tag',
							'field' => 'slug',
							'terms' => 'piggybaq_product',
							'operator' => 'NOT IN'
						)
					),
					'posts_per_page' => -1
				));

				return $loop;
						
			}

			public function get_product_request ( $id ) {
				
				$url = $this->get_service_url() . '/api/productrequest/' . $id;

				$response = wp_remote_get( $url, array(
					'sslverify' => false,
				));
				$data = null;
				if( is_array($response) ) {
					$header = $response['headers']; // array of http header lines
					$body = $response['body']; // use the content
					
					$body_data = json_decode($body, true);
					if ( $body_data['status'] && $body_data['status'] == 'OK' ) {
						$data = $body_data['data'];
					}
				}
			
				return $data;
			}

			public function process_set_default_discount($post){
				global $wpdb;

				$default_category = sanitize_text_field($post["default_category"]);
				$default_discount = intval($post["default_discount"]);
				// get Store and Config for piggybaq
			    $config_store_id = $this->get_config('store_id');
				$config_user_id = $this->get_config('user_id');
				$custom_discount = intval($post["custom_discount"]);
				if(!empty($custom_discount)){
					$default_discount = intval($post["custom_discount"]);
				}

				$table_name = $this->config_tbl;
				$db_default_discount = $wpdb->get_var("SELECT config_value FROM $table_name WHERE config_name = 'default_discount'");

				if($db_default_discount != NULL){
					$data =  array('config_value' => $default_discount );
					$where = array('config_name' => 'default_discount', );

					$wpdb->update( $table_name, $data, $where);
				}else {
					$data = array(
						'config_name' => 'default_discount',
						'config_value' => $default_discount
					);
					$wpdb->insert( $table_name, $data );
				}


				$url = $this->get_service_url() . '/api/setdefaults/' .$config_user_id. '/'.$config_store_id ;

				$response = wp_remote_post($url, array(
					'body' => array(
						'default_category' => $default_category,
						'default_discount' => $default_discount
					),
					'sslverify' => false
				));

				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
				}

			}

			public function set_icon_position($position){
				global $wpdb;

				$table_name = $this->config_tbl;

				$icon_position = $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = 'icon_position'" );

				if ( $icon_position ) {
					$data = array(
						'config_value' => $position
					);
					$where = array(
						'config_name' => 'icon_position'
					);
					$wpdb->update( $table_name, $data, $where );

				} else {
					$data = array(
						'config_name' => 'icon_position',
						'config_value' => $position
					);
					$wpdb->insert( $table_name, $data );

				}

			}

			public function disable_my_product( $product_id ) {
				global $wpdb;

				$table_name = $this->disabled_tbl;
				$db_product_id = $wpdb->get_var("SELECT product_id FROM $table_name WHERE product_id = $product_id");

				if(empty($db_product_id)){
					$data =  array('product_id' => $product_id );

					$wpdb->insert( $table_name, $data);
				}
			}

			public function enable_my_product( $product_id ) {
				global $wpdb;

				$table_name = $this->disabled_tbl;
				$db_product_id = $wpdb->get_var("SELECT product_id FROM $table_name WHERE product_id = $product_id");

				if(!empty($db_product_id)){
					$data =  array('product_id' => $product_id );

					$wpdb->delete( $table_name, $data);
				}
			}

			public function get_enabled( $product_id) {
				global $wpdb;

				$table_name = $this->disabled_tbl;
				$db_product_id = $wpdb->get_var("SELECT product_id FROM $table_name WHERE product_id = $product_id");

				if($db_product_id){
					return false;
				}
				else{
					return true;
				}
			}

			public function delete_product($get){

				$product_id = sanitize_text_field( $get['pid'] );

				wp_delete_post( $product_id, true );
				$request_id = sanitize_text_field( $get['delete'] );

				$url = $this->get_service_url() . '/api/deleterequest/' .$request_id;
				wp_remote_get( $url,array(
					'sslverify' => false,
				));

			}

            // This function is called in piggybaq-functions.php
			public function process_update_request($post) {
				
				$post_id = $post["post_id"];
				$updated_price = (float)$post["newprice"];
				$updated_categories = $post["category"];
				
				$product = new WC_Product($post_id);
				$request_id = $post["request_id"];
				$your_price = $post["yourprice"];

				wp_set_post_terms( $post_id, $updated_categories, 'product_cat');
				$this->get_product_request ( $post_id );

				$product = get_product($post_id);
				$productType = $product->product_type;

					if( $product->is_on_sale() == 1 ) {

						update_post_meta($post_id, '_sale_price', $updated_price );
						update_post_meta($post_id, '_price', $updated_price );
					} else {
						
						update_post_meta($post_id, '_regular_price', $updated_price );
						update_post_meta($post_id, '_price', $updated_price );
					}

				 if( $productType == 'variable'  ){

					$product = get_product($post_id);
					$available_variations = $product->get_available_variations();
					$productData = $this->get_product_request($request_id);
					$sellingPrice = $productData["selling_price"];
					$discountedPrice = $productData["discounted_price"];
					
					foreach($available_variations as $variations){	
				 	// Getting the variable id of the product. 
				 	$variation_id = $variations['variation_id']; 
					$variableProduct = new WC_Product_Variation( $variation_id );
 					$regularPrice = $variableProduct ->regular_price;
 					// Calculate additional cost or change in the price
					$additionalPrice = $updated_price - ($sellingPrice + $discountedPrice);
					// Add additional cost to the old price  of every variation
					$newPrice = $regularPrice + $additionalPrice;
						if( $product->is_on_sale() == 1 ) {
							
							update_post_meta($variation_id, '_sale_price', $newPrice );
							update_post_meta($variation_id, '_price', $newPrice );
						} else {

							update_post_meta($variation_id, '_regular_price', $newPrice );
							update_post_meta($variation_id, '_price', $newPrice );
						}

						$details[$variation_id] = $newPrice;
					}
					//Update min and max prices of variation
					update_post_meta($post_id, '_min_variation_price', min($details) );
					update_post_meta($post_id, '_max_variation_price', max($details) );
					update_post_meta($post_id, '_min_variation_regular_price', min($details) );
					update_post_meta($post_id, '_max_variation_regular_price', max($details) );
					
					//Update ids of min and max prices of variation
					update_post_meta($post_id, '_min_price_variation_id', array_search (min($details), $details) );
					update_post_meta($post_id, '_max_price_variation_id', array_search (max($details), $details) );
					update_post_meta($post_id, '_min_regular_price_variation_id', array_search (min($details), $details) );
					update_post_meta($post_id, '_max_regular_price_variation_id', array_search (max($details), $details) );
				}

				// Send price to Node api server
				$url = $this->get_service_url() . '/api/productupdate/' .$request_id ;

				wp_remote_post($url,array(
					'body' => array('your_price' => $your_price),
					'sslverify' => false
				));


			}

			public function curl_installed() {
				return in_array( 'curl', get_loaded_extensions() );
			}

		    /*
		    * Developer: Asim Bilal
		    * Name: check_piggybaq
		    * Description: woo commerce hook fire whenever order is completed
		    * Params: order_id = order id of current order
		    */			
			public function check_piggybaq ( $order_id ) {
				
				//order object
				$order = new WC_Order( $order_id );
				$config_store_id = $this->get_config('store_id');
				$config_service_url = $this->get_service_url();


				//count check of order
				if ( count( $order->get_items() ) > 0 ) {
					foreach( $order->get_items() as $key => $lineItem ) {
						if(  $lineItem['variation_id'] ){
							$_product = wc_get_product( $lineItem['variation_id'] );
							$variation_data = $_product->get_variation_attributes();
							$variation_detail = woocommerce_get_formatted_variation( $variation_data, true );                           
							$product_name = $lineItem['name'] . " " . $variation_detail;
						} else {
							$_product = wc_get_product( $lineItem['product_id'] );
							$product_name = $lineItem['name'];
						}
						$response = wp_remote_post( $config_service_url . '/api/notifyProductPurchase?type=wooCommerce', array(
							'method' => 'POST',
							'body' => array(
								'store_id' => $config_store_id,
								'product_id' => $lineItem['product_id'],
								'product_name' => $product_name,
								'quantity' => $lineItem['qty'],
								'price' => $_product->get_price(),
								'order_id' => $order_id,
								'shipping_first_name' => $order->shipping_first_name,
								'shipping_last_name' => $order->shipping_last_name,
								'shipping_company' => $order->shipping_company,
								'shipping_address_1' => $order->shipping_address_1,
								'shipping_address_2' => $order->shipping_address_2,
								'shipping_city' => $order->shipping_city,
								'shipping_state' => $order->shipping_state,
								'shipping_postcode' => $order->shipping_postcode,
								'shipping_country' => $order->shipping_country,
								'shipping_phone' => $order->billing_phone
							),
							'sslverify' => false
						));
						if ( is_wp_error( $response ) ) {
							error_log("Error Occured");
							$error_message = $response->get_error_message();
							error_log($error_message);                          
						} else {
							error_log("Succefully");
						}

					}
				}
			}


			public function get_blur() {
				// keep reloading the page on blur and focus when in setup screen
				echo 	'<script>';
				echo 		'window.onblur=function() {';
				echo 			'window.onfocus= function () {';
				echo 				'location.reload(true);';
				echo 			'}';
				echo 		'};';
				echo 	'</script>';

			}
		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['wc_piggybaq'] = new WC_Piggybaq();

		if ( isset ($_GET['callback_listner']) && isset ($_GET['store_id']) && isset ($_GET['user_id']) && isset ($_GET['default_discount']) && isset ($_GET['store_user']) ) {
			// update_option("piggybaq_store_id", $_GET["store_id"]);
			
			$table_name = $GLOBALS['wc_piggybaq']->config_tbl;
			$store_id = $_GET["store_id"];
			$user_id = $_GET['user_id'];
			$default_discount = $_GET['default_discount'];
			$store_user = $_GET['store_user'];
			
			$config_store_id = $wpdb->get_var( "SELECT * FROM $table_name WHERE config_name = 'store_id'" );

			if ( $config_store_id ) {
				$data = array(
					'config_value' => $store_id
				);
				$where = array(
					'config_name' => 'store_id'
				);
				$wpdb->update( $table_name, $data, $where );

			} else {
				$data = array(
					'config_name' => 'store_id',
					'config_value' => $store_id
				);
				$wpdb->insert( $table_name, $data );

			}

			$config_user_id = $wpdb->get_var( "SELECT * FROM $table_name WHERE config_name = 'user_id'" );

			if ( $config_user_id ) {
				$data = array(
					'config_value' => $user_id
				);
				$where = array(
					'config_name' => 'user_id'
				);
				$wpdb->update( $table_name, $data, $where );

			} else {
				$data = array(
					'config_name' => 'user_id',
					'config_value' => $user_id
				);
				$wpdb->insert( $table_name, $data );

			}

			$config_default_discount = $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = 'default_discount'" );

			if ( $config_default_discount ) {
				$data = array(
					'config_value' => $default_discount
				);
				$where = array(
					'config_name' => 'default_discount'
				);
				$wpdb->update( $table_name, $data, $where );

			} else {
				$data = array(
					'config_name' => 'default_discount',
					'config_value' => $default_discount
				);
				$wpdb->insert( $table_name, $data );

			}

			$config_store_user = $wpdb->get_var( "SELECT config_value FROM $table_name WHERE config_name = 'store_user'" );

			if ( $config_store_user ) {
				$data = array(
					'config_value' => $store_user
				);
				$where = array(
					'config_name' => 'store_user'
				);
				$wpdb->update( $table_name, $data, $where );

			} else {
				$data = array(
					'config_name' => 'store_user',
					'config_value' => $store_user
				);
				$wpdb->insert( $table_name, $data );

			}

		}
	}
}