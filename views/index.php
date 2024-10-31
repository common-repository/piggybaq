<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/** 
 * check function admin_plugin_page in woocommerce-piggybaq.php
 **/

$url = "";
if ( !$config_store_id || !$config_user_id ) {
    if ( $woocommerce_api_consumer_key != "" && $woocommerce_api_consumer_secret != "" && $woocommerce_api_key_permissions == "read_write" ) {
        $url = "?key=" . urlencode($woocommerce_api_consumer_key);
        $url .= "&secret=" . urlencode($woocommerce_api_consumer_secret);
        $url .= "&type=" . urlencode("woocommerce");
        $url .= "&store=" . urlencode($store);
        $url .= "&default_category=" . urlencode($first_category);
        $url .= "&endpoint=" . urlencode($endpoint);
        $url .= "&callback=" . urlencode($callback);
    }
}

?>
<!-- <div class="wrap">
		
</div>
 -->

<?php if ( !$config_store_id || !$config_user_id ) { 
    add_action( 'admin_print_scripts', $this->get_blur() );
    ?>



<header>
    <div class="container">
        <div class="logo logocenter"><img src="<?php echo esc_url(plugins_url( '../images/logoCircle.png', __FILE__ )); ?>"></div>
        <div class="clearify"></div>
    </div>
</header>
<section class="piggySetupContain">
    <div class="piggySetupInner">
        <div class="piggySetupText CenterText"><img src="<?php echo esc_url(plugins_url( '../images/easysteps.png', __FILE__ )); ?>" /></div>
        <div class="clearify"></div>
    </div>


    <?php $step1 = !$this->curl_installed() || !$this->get_hello(); ?>
    <div class="piggySetupInner">
        <?php if ( $step1 ) { ?>
            <div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggyunchecked.png', __FILE__ )); ?>" /></div>
        <?php } else { ?>
            <div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
        <?php } ?>

        <?php if ( $step1 ) { ?>
            <div class="piggySetupText">Connection to piggybaQ platform couldn't be established 
                <div class="piggysmallText">
                Contact your hosting provider / administrator to enable cURL for PHP
                </div>
            </div>
			
            <div class="piggySetupLinks"><a class="goBtn" href="http://php.net/manual/en/curl.installation.php" target="_blank">HELP</a></div>
            <div class="clearify"></div>
                
			<?php } else { ?>
				<div class="piggySetupText">Connection to piggybaQ platform is established </div>
			<?php } ?>
			<div class="clearify"></div>
		</div>

		<?php $step2 = $woocommerce_api_enabled == 'no'; ?>
		<div class="piggySetupInner">
			<?php if ( $step2 ) { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggyunchecked.png', __FILE__ )); ?>" /></div>
			<?php } else { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
			<?php } ?>

			<div class="piggySetupText">Enable Rest API </div>
			<?php if ( $step2 ) { ?>
				<div class="piggySetupLinks"><a class="goBtn" target="_blank" href="<?php echo esc_url($this->get_service_url_not_ssl() . '/#/setupframe/' . str_replace('/','-',base64_encode(admin_url('admin.php?page=wc-settings#woocommerce_demo_store'))) . '/' . rawurlencode('Enable Rest API')); ?>">GO</a></div>
                <div class="clearify"></div>
                <div class="piggybaqScreenShot"><img src="<?php echo esc_url(plugins_url( '../images/screen4.jpg', __FILE__ )); ?>" /></div>
			<?php } ?>
			<div class="clearify"></div>
		</div>

		<?php $step3 = $woocommerce_api_consumer_key == '' || $woocommerce_api_consumer_secret == ''; ?>
		<div class="piggySetupInner">
			<?php if ( $step3 ) { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggyunchecked.png', __FILE__ )); ?>" /></div>
			<?php } else { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
			<?php } ?>

			<div class="piggySetupText">Generate API consumer and secret key </div>
			<?php if ( !$step2 && $step3 ) { ?>

				<div class="piggySetupLinks"><a class="goBtn" target="_blank" href="<?php echo esc_url($this->get_service_url_not_ssl() . '/#/setupframe/' . str_replace('/','-',base64_encode(admin_url('profile.php#woocommerce_generate_api_key'))) . '/' . rawurlencode('Generate API consumer and secret key')); ?>">GO</a></div>
                <div class="clearify"></div>
                <div class="piggybaqScreenShot"><img src="<?php echo esc_url(plugins_url( '../images/screen1.jpg', __FILE__ )); ?>" /></div>
			<?php } ?>
			<div class="clearify"></div>
		</div>

		<?php $step4 = ( $woocommerce_api_consumer_key != '' && $woocommerce_api_consumer_secret != '' ) && $woocommerce_api_key_permissions != "read_write"; ?>
		<div class="piggySetupInner">
			<?php if ( $step3 || $step4 ) { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggyunchecked.png', __FILE__ )); ?>" /></div>
			<?php } else { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
			<?php } ?>

			<div class="piggySetupText">Allow Read/Write permissions to your API Keys </div>
			<?php if ( $step4 ) { ?>
				<div class="piggySetupLinks"><a class="goBtn" target="_blank" href="<?php echo esc_url($this->get_service_url_not_ssl() . '/#/setupframe/' . str_replace('/','-',base64_encode(admin_url('profile.php#woocommerce_api_consumer_key'))) . '/' . rawurlencode('Allow Read Write permissions to your API Keys')); ?>">GO</a></div>
                <div class="clearify"></div>
                <div class="piggybaqScreenShot"><img src="<?php echo esc_url(plugins_url( '../images/screen2.jpg', __FILE__ )); ?>" /></div>
			<?php } ?>
			<div class="clearify"></div>
		</div>

		<?php $step5 = ( $woocommerce_api_consumer_key != '' && $woocommerce_api_consumer_secret != '' ) && $woocommerce_api_key_permissions == "read_write"; ?>
		<div class="piggySetupInner">
			<?php if ( $step1 || $step2 || $step3 || $step4 || $step5 ) { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggyunchecked.png', __FILE__ )); ?>" /></div>
			<?php } else { ?>
				<div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
			<?php } ?>

			<div class="piggySetupText">Setup your store with piggybaQ </div>
			<?php if ( $step5 ) { ?>
				<div class="piggySetupLinks"><a class="goBtn" href="#" onclick="piggybaq.openPop('<?php echo $this->get_service_url(); ?>/api/registerstore<?php echo $url; ?>');">GO</a></div>
                <?php
					//div class="clearify"></div>
                	//<div class="piggybaqScreenShot"><img src="<?php echo plugins_url( '../images/screen3.jpg', __FILE__ )" /></div>
				?>
			<?php } ?>
			<div class="clearify"></div>
		</div>
</section>
	<?php } else { ?>
<section class="piggySettingsContain">
			<div>
				<form name="disablePiggybaq" method="post">
                	<div class="piggyFormLeft">	
                    	<h2>Import Settings</h2>
                        <div class="piggybaqFormDiv piggybaqFormDivFirst">
                            <div class="piggyFormLabel">
                            	Default Category
                            	<div class="piggyFormText">Products you are importing from other stores will be assigned to this category.</div>
                            </div>
                            <div class="piggyFormField">
				        		<select name = "default_category" id="default_value">
									<?php 
				        
				                        $taxonomy     = 'product_cat';
				                        $orderby      = 'name';  
				                        $show_count   = 0;      // 1 for yes, 0 for no
				                        $pad_counts   = 0;      // 1 for yes, 0 for no
				                        $hierarchical = 1;      // 1 for yes, 0 for no  
				                        $title        = '';  
				                        $empty        = 0;
				        
				                        $args = array(
				                                     'taxonomy'     => $taxonomy,
				                                     'orderby'      => $orderby,
				                                     'show_count'   => $show_count,
				                                     'pad_counts'   => $pad_counts,
				                                     'hierarchical' => $hierarchical,
				                                     'title_li'     => $title,
				                                     'hide_empty'   => $empty
				                        );
				                        $all_categories = get_categories( $args ); 
				                    	foreach ($all_categories as $cat) {
				                            if($cat->category_parent == 0) {
				                                    $category_id = $cat->term_id; ?>
				                                            <option <?php echo $default_category == $cat->name ? 'selected':''; ?> value="<?php echo esc_html($cat->name); ?>"><?php echo esc_html($cat->name);?></option>
				                                <?php  $args2 = array(
				                                                'taxonomy'     => $taxonomy,
				                                                'child_of'     => 0,
				                                                'parent'       => $category_id,
				                                                'orderby'      => $orderby,
				                                                'show_count'   => $show_count,
				                                                'pad_counts'   => $pad_counts,
				                                                'hierarchical' => $hierarchical,
				                                                'title_li'     => $title,
				                                                'hide_empty'   => $empty
				                                        );
				                                $sub_cats = get_categories( $args2 );
				                                if($sub_cats) {  ?>
				                                <?php	foreach($sub_cats as $sub_category) { ?>
				                                                    <option <?php echo $default_category == $sub_category->name ? 'selected':''; ?> value="<?php echo  esc_html($sub_category->name); ?>"><?php echo esc_html($sub_category->name); ?></option>                                   
				                                  <?php } ?>  
				                          <?php }	
				                          	} ?>       
				                    <?php	} ?>
				                </select>
				            </div>
                            <div class="clearify"></div>
                        </div>
                    </div>
                    <div class="piggyFormRight">
                    	<h2>Export Settings</h2>
                    	<div class="piggybaqFormDiv">
                            <div class="piggyFormLabel">
                            	Default Commission
                                <div class="piggyFormText">This is the commission that resellers will receive.</div>
                            </div>
                            <div class="piggyFormField piggyRadioBtns">
                            	<div>
                                    <div class="piggyRadioLeft">
                                    <input class="default_discount" name="default_discount" id="default_discount1" type="radio" value="0" <?php echo $default_discount == 0 ? 'checked':''; ?> />0%&nbsp;&nbsp;
                                    </div>
                                    <div class="piggyRadioRight">
                                    <input class="default_discount" name="default_discount" id="default_discount2" type="radio" value="10" <?php echo $default_discount == 10 ? 'checked':''; ?> />10%&nbsp;&nbsp;
                                    </div>
                                    <div class="clearify"></div>
                                </div>
                                <div class="inputThird">
                                    <div class="piggyRadioLeft">
                                    <input class="default_discount" name="default_discount" id="default_discount3" type="radio" value="15" <?php echo $default_discount == 15 ? 'checked':''; ?> />15%&nbsp;&nbsp;
                                    </div>
                                    <div class="piggyRadioRight">
                                    <input class="default_discount" name="default_discount" id="default_discount4" type="radio" value="custom" <?php echo $default_discount != 0 && $default_discount != 10 && $default_discount != 15  ? 'checked':''; ?> />
                                    <input type="text" class="customDiscount" name="custom_discount" id="custom_discount" value="<?php echo $default_discount != 0 && $default_discount != 10 && $default_discount != 15 ? $default_discount : ''; ?>" placeholder="Custom" /> %
                                    </div>
                                    <div class="clearify"></div>
                                </div>
                            </div>
                            <div class="clearify"></div>
                        </div>
                    </div>
                    <div class="piggyFormLeft piggyFormLast">	
                    	<h2>Icon Placement</h2>
                    	<div>
                            <div class="piggyIconPos">
                                <div class="piggyIconPosTitle">Product</div>
                                <div class="piggyWhiteCircle piggyTopLeft" data-value="piggyTopLeft"><img src="<?php echo esc_url(plugins_url( '../images/piggybaq.png', __FILE__ )); ?>" /></div>
                                <div class="piggyWhiteCircle piggyTopRight" data-value="piggyTopRight"><img src="<?php echo esc_url(plugins_url( '../images/piggybaq.png', __FILE__ )); ?>" /></div>
                                <div class="piggyWhiteCircle piggyBottomLeft" data-value="piggyBottomLeft"><img src="<?php echo esc_url(plugins_url( '../images/piggybaq.png', __FILE__ )); ?>" /></div>
                                <div class="piggyWhiteCircle piggyBottomRight" data-value="piggyBottomRight"><img src="<?php echo esc_url(plugins_url( '../images/piggybaq.png', __FILE__ )); ?>" /></div>
                            </div>
                        </div>
                        <div class="piggyFormText piggyIconText">Choose where you would like the piggybaQ icon to be placed on your product.</div>
                        <div class="piggyupdateBtn">
                        	<input type="hidden" name="icon_postion" id="iconPostion" value="<?php echo esc_html($default_icon_position); ?>" />
                            <input name="save" type="submit" class="button button-primary button-large" id="publish" value="Update">
                        </div>
                    </div>
                    <div class="clearify"></div>
				</form> 
			</div>
	<?php  }  ?>
<?php if($config_store_id && $config_user_id && $this->get_config('is_disable') == 0) { 
		$product_list = $this->get_products();
		$woo_product_list = $this->get_woo_products();
		$args = array(
		    'post_type' => 'product',
		    'showposts' => '1000',
		    'product_tag' => 'piggybaq_product'
		);
		$query = new WP_Query( $args );
		$piggybaq_count = $query->post_count;	
?>
	<div >
        <ul class="subsubsub" >
            <li class="all"><a id="piggybaq_products_links" class="current" onclick="piggybaq.toggleProductList(this, 'piggybaq'); return false;">PiggybaQ Products <span class="count">(<?php echo esc_html($piggybaq_count); ?>)</span></a> |</li>
            <li class="publish"><a id="my_products_links" onclick="piggybaq.toggleProductList(this, 'my');">My Products <span class="count">(<?php echo esc_html(count($woo_product_list)); ?>)</span></a></li>
    	</ul>
    </div>
    <div>
		<table id="piggybaq_products" class="wp-list-table widefat fixed striped posts" cellpadding="0" cellspacing="0">
			<tr>
				<th>Name</th>
				<th>Category</th>
				<th>Price</th>
				
			</tr>
			<?php if ( count($product_list) > 0 ) { ?>
			
				<?php 
				foreach ( $product_list as $p ) { 
					$product = new WC_Product( $p['requester_product_id'] );
						if($product->post) {
						$product_categories = wp_get_post_terms( $p['requester_product_id'], 'product_cat' );
						$output = array_map(function ($object) { return $object->name; }, $product_categories);
						$category_output = implode(', ', $output);
						$price = $p['discounted_price'] + $p['selling_price'];

					?>
						<tr>
		                	<td>
		                        <strong><a class="row-title" target="_blank" href="<?php echo esc_url(get_permalink($product->id)); ?>"><?php echo esc_html($product->post->post_title); ?></a></strong>
		                        <div class="row-actions">
		                            <span class="id">ID: <?php echo esc_html($p['requester_product_id']); ?> | </span>
		                            <span class="edit"><a href="<?php echo esc_url(admin_url("admin.php?page=".$_GET["page"] . '&action=edit&id='. $p['_id'])); ?>" title="Edit this item">Edit</a> | </span>
		                            <span class="trash"><a onClick="return confirm('Are you sure you want to delete?')" class="submitdelete" title="Move this item to the Trash" href="<?php echo esc_url(admin_url("admin.php?page=".$_GET["page"] . '&delete='. $p['_id'].'&pid='. $p['requester_product_id'])); ?>">Trash</a> | </span>
		                            <span class="view"><a href="<?php echo esc_url(get_permalink($product->id)); ?>" target="_blank" title="<?php echo esc_html($product->post->post_title); ?>" rel="permalink">View</a> | </span>
		                        </div>
		                    </td>
		                    <td><?php echo esc_html($category_output) ?></td>
		                    <td><?php echo esc_html($price); ?></td>
		                    
						</tr>
						<?php } ?>
					<?php } ?>
			<?php } else { // if count = 0 ?>
				<tr class="no-items"><td class="colspanchange" colspan="4">No products found.</td></tr>
			<?php } ?>
			<tr>
				<th>Name</th>
				<th>Category</th>
				<th>Price</th>
				
			</tr>
			
		</table>
	</div>

	<div>
		<table id="my_products" style="display:none;" class="wp-list-table widefat fixed striped posts" cellpadding="0" cellspacing="0">
			<tr>
				<th>Name</th>
				<th>Category</th>
				<th>Price</th>
				<th align="center" style="text-align:center">Enable piggybaQ</th>
			</tr>
			<?php if ( count($woo_product_list) > 0 ) { ?>
			
				<?php 
				foreach ( $woo_product_list as $p ) { 
					$product_categories = wp_get_post_terms( $p->ID, 'product_cat' );
					$output = array_map(function ($object) { return $object->name; }, $product_categories);
					$category_output = implode(', ', $output);
					
					$product = new WC_Product( $p );
					
				?>
					<tr>
	                	<td>
	                        <strong><a class="row-title" href="#"><?php echo esc_html($p->post_title); ?></a></strong>
	                        <div class="row-actions">
	                            <span class="id">ID: <?php echo esc_html($p->ID); ?> | </span>
	                            <span class="edit"><a href="<?php echo esc_url(get_edit_post_link($p->ID)); ?>" target="_blank" title="Edit this item">Edit</a> | </span>
	                            <span class="view"><a href="<?php echo esc_url(get_permalink($p->ID)); ?>" target="_blank" title="<?php echo esc_html($product->post->post_title); ?>" rel="permalink">View</a> | </span>
	                        </div>
	                    </td>
	                    <td><?php echo esc_html($category_output); ?></td>
	                    <td><?php echo esc_html($product->price); ?></td>
	                    <td align="center" id="nav">
	                    	<?php if ( $this->get_enabled($p->ID) ) { ?>
	                    		<span class="disable"><a href="<?php echo esc_url(admin_url("admin.php?page=".$_GET["page"] . '&disabled='. $p->ID . '#my_products_link')); ?>" title="Disable this item">Disable</a></span>
	                    	<?php } else { ?>
	                    		<span class="enable"><a href="<?php echo esc_url(admin_url("admin.php?page=".$_GET["page"] . '&enabled='. $p->ID . '#my_products_link')); ?>" title="Enable this item">Enable</a></span>
	                    	<?php } ?>
	                    </td>
	                    
					</tr>
				<?php } ?>
			<?php } else { // if count ?>
				<tr class="no-items"><td class="colspanchange" colspan="4">No products found.</td></tr>
			<?php } ?>
			<tr>
				<th>Name</th>
				<th>Category</th>
				<th>Price</th>
				<th align="center" style="text-align:center">Enable piggybaQ</th>
			</tr>
			
		</table>
	</div>

        <div class="piggySetupInner piggySetupInner2">
            <div class="piggyCheck"><img src="<?php echo esc_url(plugins_url( '../images/piggychecked.png', __FILE__ )); ?>" /></div>
            <div class="piggySetupText">Store linked to piggybaQ with ID: <?php echo esc_html($store_user); ?></div>
            <div class="clearify"></div>
        </div>
</section>
<?php  } ?>
