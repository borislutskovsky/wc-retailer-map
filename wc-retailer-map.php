<?php
/*
 * Plugin Name: WooCommerce Retailer Map
 * Description: a plugin that manages retailers.
 * Version: 1.0
 * Author: Boris Lutskovsky
 * Author URI: http://www.iamboris.com
 * License: MIT
 */

global $wc_retailer_map_version;
$wc_retailer_map_version = "1.0";

function wc_retailer_map_install(){
  global $wpdb, $wc_retailer_map_version;

  $table_name = $wpdb->prefix . 'wc_retailers';
  $charset_collate = $wpdb->get_charset_collate();

  add_option('wc_retailer_map_version', $wc_retailer_map_version);
}


add_action('init', 'wc_retailers_register_post_type');
function wc_retailers_register_post_type(){
  register_post_type('retailer', array(
    'labels' => array('name' => __('Retailers'), 'singular_name' => __('Retailer'), 'add_new_item' => __('Add New Retailer')),
    'public' => true,
    'has_archive' => true,
    'rewrite' => array('slug' => 'retailers'),
    'supports' => array('title', 'revisions', 'page-attributes'),
    'menu_icon' => 'dashicons-location-alt',
    'register_meta_box_cb' => 'wc_retailer_post_additional_fields'
  ));
}

function wc_retailer_post_additional_fields($post){
  add_meta_box('wc_retailer_map_address', 'Physical Address', 'wc_retailer_metabox_address_cb', 'retailer', 'advanced', 'high');
  add_meta_box('wc_retailer_map_contact', 'Contact Info', 'wc_retailer_metabox_contact_cb', 'retailer', 'advanced', 'high');
}

function wc_retailer_metabox_contact_cb($post, $metabox){
  $url = get_post_meta($post->ID, 'wc_retailer_map_url', true);
  ?>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_url" class="post-attributes-label"><?php echo __('Website'); ?></label></p>
  <input name="wc_retailer_map_url" value="<?php echo $url; ?>" />

  <?php
}
function remove_yoast_metabox(){
    remove_meta_box('wpseo_meta', 'retailer', 'normal');
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox',11 );


function wc_retailer_metabox_address_cb($post, $metabox){

  $address = get_post_meta($post->ID, 'wc_retailer_map_address', true);
  $city = get_post_meta($post->ID, 'wc_retailer_map_city', true);
  $state = get_post_meta($post->ID, 'wc_retailer_map_state', true);
  $country = get_post_meta($post->ID, 'wc_retailer_map_country', true);
  $postal_code = get_post_meta($post->ID, 'wc_retailer_map_postal', true);

  ?>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_address" class="post-attributes-label"><?php echo  __('Address'); ?></label></p>
  <input name="wc_retailer_map_address" value="<?php echo $address; ?>" />
  <br/>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_city" class="post-attributes-label"><?php echo __('City'); ?></label></p>
  <input name="wc_retailer_map_city" value="<?php echo $city; ?>" />
  <br/>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_state" class="post-attributes-label"><?php echo __('State'); ?></label></p>
  <input name="wc_retailer_map_state" value="<?php echo $state; ?>" />

  <?php
}

function wc_retailer_map_uninstall(){

}
register_activation_hook(__FILE__, 'wc_retailer_map_install');
register_uninstall_hook(__FILE__, 'wc_retailer_map_uninstall');


add_action('admin_menu', 'wc_retailer_admin_page');