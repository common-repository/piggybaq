
<?php 

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if( isset($_GET['id'])) {?>

<section class="piggySettingsContain">
    <a name="back" href="<?php echo esc_url(admin_url("admin.php?page=piggybaq-settings")); ?>" class="button button-primary button-large" id="back" >&lt; Back</a>
	<div class="wrap">
        <h2>Edit Product</h2>
    </div>
	<div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
        <form id="edit_Product" name="updateproduct" action="" method="post">
            <div id="post-body-content">
                <section class="piggybaqEditForm">
                 <?php 
						$request = $this->get_product_request( sanitize_text_field($_GET['id']) );
						$request_id = sanitize_text_field($_GET['id']);
						$pid = sanitize_text_field($request['requester_product_id']);
                        $default_discount = intval($this->get_config('default_discount'));
                        $product_cats = wp_get_post_terms( $pid, 'product_cat' );

                        $default_discount
                 ?>
                    <input type="hidden" name="discountprice" id="discountprice" value="<?php echo esc_html($request['discounted_price']); ?>" />
                     <input type="hidden" name="discountprice" id="owneroriginalprice" value="<?php echo esc_html($request['owner_original_price']); ?>" />
                    <input type="hidden" name="post_id" id="post_id" value="<?php echo esc_html($request['requester_product_id']); ?>" />
                    <input type="hidden" name="request_id" id="request_id" value="<?php echo esc_html($request_id); ?>" />
                    <input type="hidden" name="default_discount" id="default_discount" value="<?php echo esc_html($default_discount); ?>" />
                    <div class="confirmPriceDiv">
                        <div class="confirmProductPrice"><div class="price ng-binding"><?php echo esc_html(get_woocommerce_currency_symbol()); ?><?php echo esc_html(sprintf("%.2f", $request['selling_price'])); ?><input type="hidden" name="sellprice" id="sellprice" placeholder="New Selling Price" value="<?php echo esc_html(sprintf("%.2f", $request['selling_price'])); ?>"/></div><div class="text">Seller</div></div>
                        <div class="confirmPriceSign">+</div>
                        <div class="confirmProductPrice confirmProductPrice2"><div><input type="text" name="yourprice" id="yourprice" onkeyup="piggybaq.priceChanged()" placeholder="New Discounted Price" value="<?php echo esc_html(sprintf("%.2f", $request['discounted_price'])); ?>" /></div><div class="text">You</div></div>
                        <div class="confirmPriceSign">=</div>
                        <div class="confirmProductPrice confirmProductPrice3"><div class="price ng-binding"><span id="newpriceSpan"><?php echo esc_html(get_woocommerce_currency_symbol()); ?><?php echo esc_html(sprintf("%.2f", ($request['selling_price'] + $request['discounted_price']))); ?></span><input type="hidden" name="newprice" id="newprice" placeholder="New Total Price" value="<?php echo esc_html(sprintf("%.2f", ($request['selling_price'] + $request['discounted_price']))); ?>"/></div><div class="text">Resell Price</div></div>
                        <div class="clearify"></div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                      <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div id="product_catdiv" class="postbox " style="display: block;">
                          <h3 class="hndle"><span>Choose Categories</span></h3>
                          <div class="inside">
                            <div id="taxonomy-product_cat" class="categorydiv">
                              <div id="product_cat-all" class="tabs-panel">
                                <ul id="product_catchecklist" data-wp-lists="list:product_cat" class="categorychecklist form-no-clear">
                                    <?php	

                                            $taxonomy = 'product_cat';
                                            $orderby = 'name';
                                            $show_count = 0; // 1 for yes, 0 for no
                                            $pad_counts = 0; // 1 for yes, 0 for no
                                            $hierarchical = 1; // 1 for yes, 0 for no
                                            $title = '';
                                            $empty = 0;

                                            $args = array(
                                            'taxonomy' => $taxonomy,
                                            'orderby' => $orderby,
                                            'show_count' => $show_count,
                                            'pad_counts' => $pad_counts,
                                            'hierarchical' => $hierarchical,
                                            'title_li' => $title,
                                            'hide_empty' => $empty
                                            );

                                            $all_categories = get_categories($args);
                                            foreach ($all_categories as $cat) {
                                                if($cat->category_parent == 0) {
                                                        $category_id = $cat->term_id;
                                                        $check = false;
                                                        $i=0;
                                                        while ($i<count($product_cats) && $check == false){
                                                            if($cat->name == $product_cats[$i]->name){ 
                                                                $check = true;  ?>
                                                                <li><input type="checkbox" name="category[]" value="<?php echo esc_html($cat->term_id); ?>" checked><?php echo esc_html($cat->name); ?>
                                                        <?php }
                                                            else{ 
                                                                    $i++;
                                                                }
                                                        } ?>                                    
                                                    <?php if($check == false ){?>
                                                                <li><input type="checkbox" name="category[]" value="<?php echo esc_html($cat->term_id); ?>"><?php echo esc_html($cat->name); ?>
                                                    <?php } ?>
                                                
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
                                                        <ul class="children">
                                                    <?php	foreach($sub_cats as $sub_category) {
                                                                $check2 = false;
                                                                $i=0;
                                                                while ($i<count($product_cats) && $check2 == false){
                                                                    if($sub_category->name == $product_cats[$i]->name){ 
                                                                        $check2 = true; ?>
                                                                        <li>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category[]" value="<?php echo esc_html($sub_category->term_id); ?>" checked><?php echo esc_html($sub_category->name); ?></li>
                                                              <?php }
                                                                    else{ 
                                                                        $i++;
                                                                    }
                                                                } ?>
                                                            <?php if($check2 == false ){?>
                                                                        <li>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="category[]" value="<?php echo esc_html($sub_category->term_id); ?>"><?php echo esc_html($sub_category->name); ?></li>
                            
                                                            <?php }
                                                            } ?>
                                                            </ul>
                                                    <?php }
                                                } ?>
                                                </li>
                                   <?php	} ?>
                                </ul>
                              </div>
                            </div>
                          </div>
                          <div id="major-publishing-actions">
                            <div id="publishing-action">
                            <span class="spinner"></span>
                                    <input name="save" type="submit" class="button button-primary button-large" onclick="return piggybaq.validaterequest()" id="update" value="Update">
                            </div>
                            <div class="clear"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                </section>
            </div>
        </form>
        </div>
    </div>
</section>

<?php } // if $_GET['id'] ?>
