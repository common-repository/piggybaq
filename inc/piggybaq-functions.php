<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if(!empty($_POST["sellprice"]) && !empty($_POST["yourprice"]) && !empty($_POST["newprice"])) {
	
	$post = array();
	$post["discountprice"] = sanitize_text_field($_POST["discountprice"]);
	$post["post_id"] = sanitize_text_field($_POST["post_id"]);
	$post["request_id"] = sanitize_text_field($_POST["request_id"]);
	$post["default_discount"] = floatval($_POST["default_discount"]);
	$post["sellprice"] = floatval($_POST["sellprice"]);
	$post["yourprice"] = floatval($_POST["yourprice"]);
	$post["newprice"] = floatval($_POST["newprice"]);
	$post["category"] =  sanitize_term($_POST["category"]);
	$post["save"] = sanitize_text_field($_POST["save"]);
	
	// This function "process_update_request" is located in woocommerce-piggybaq.php file

	$this->process_update_request( $post );
}

if( isset($_POST['disable']) ){

	$post = array();
	$post["disable"] = sanitize_text_field($_POST["disable"]);

	// This function "piggypaq_disable" is located in woocommerce-piggybaq.php file

	$this->piggybaq_disable( $post );
}

if( isset($_POST['default_category']) || isset($_POST['default_discount']) || isset($_POST['custom_discount']) ){
	
	$post = array();
	$post["custom_discount"] = floatval($_POST["custom_discount"]);
	$post["default_discount"] = floatval($_POST["default_discount"]);
	$post["default_category"] = sanitize_text_field($_POST["default_category"]);

	$this->process_set_default_discount( $post );
}

if ( isset( $_POST['icon_postion']) ) {
	
	$this->set_icon_position( sanitize_text_field($_POST['icon_postion']) );
}

if ( isset($_GET['delete'])) {

	$get = array();
	$get["page"] = sanitize_text_field( $_GET["page"] );
	$get["delete"] = sanitize_text_field( $_GET["delete"] );
	$get["pid"] = sanitize_text_field( $_GET["pid"] );

	$this->delete_product( $get );
}

if( isset($_GET['disabled'])) {
	
	$this->disable_my_product( intval($_GET['disabled']) );
}
if( isset($_GET['enabled'])) {

	$this->enable_my_product( intval($_GET['enabled']) );
}
?>