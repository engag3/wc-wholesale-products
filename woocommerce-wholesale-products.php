<?php
/**
 * Plugin Name: E3 WooCommerce Wholesale Category
 * Plugin URI: https://www.engag3.media
 * Description: Products in the "Wholesale" category will only be visible to users with the "Wholesale" user role.
 * Version: 1.0.1
 * Author: ENGAG3 Media
 * Author URI: https://www.engag3.media
 * GitHub Plugin URI: engag3/wc-wholesale-products
 * License: GPL2
 */

// Woocommerce - Redirect unauthorised users from accessing a specified product category when clicked or visited via direct url

function woocommerce_hide_non_registered() {
 if( ( is_product_category('wholesale') ) && ! ( current_user_can( 'wholesale' ) || current_user_can( 'shop_manager' ) || current_user_can( 'administrator' ) ) ) {
 wp_redirect( site_url( '/' ));
 exit();
 }
}
add_action( 'template_redirect','woocommerce_hide_non_registered' );
// End - Woocommerce - redirect unauthorised users from accessing a specified product category
// Woocommerce - Removes category link from woocommerce product category widgets so they are not seen
add_filter( 'get_terms', 'get_subcategory_terms', 10, 3 );
function get_subcategory_terms( $terms, $taxonomies, $args ) {
  $new_terms = array();
  // if a product category and on the shop page
  if ( in_array( 'product_cat', $taxonomies ) && ! ( current_user_can( 'wholesale' ) || current_user_can( 'administrator' ) ) ) {
    foreach ( $terms as $key => $term ) {
      if ( ! in_array( $term->slug, array( 'wholesale' ) ) ) {
        $new_terms[] = $term;
      }
    }
    $terms = $new_terms;
  }
  return $terms;
}
// End - Woocommerce - Removes category link from woocommerce product category widgets so they are not seen
// Woocommerce - Remove products from being displayed that belong to a category user is not authorised to visit. Products seem to still be accessible via direct url unfortunately.
add_action( 'pre_get_posts', 'custom_pre_get_posts' );
function custom_pre_get_posts( $q ) {
		if ( ! $q->is_main_query() ) return;
		if ( ! $q->is_post_type_archive() ) return;
		if ( ! ( current_user_can( 'wholesale' ) || current_user_can( 'administrator' ) ) ) {
			   $q->set( 'tax_query', array(array(
				   'taxonomy' => 'product_cat',
				   'field' => 'slug',
				   'terms' => array( 'wholesale'), // Don't display products in the private-clients category on the shop page
				   'operator' => 'NOT IN'
			   )));
		}
remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );
}
// End - Woocommerce - Remove products from being displayed that belong to a category user is not authorised to visit. Products seem to still be accessible via direct url unfortunately.
