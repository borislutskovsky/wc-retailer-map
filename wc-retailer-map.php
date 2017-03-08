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

  wp_enqueue_script('angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.2/angular.min.js');

  wp_enqueue_script( 'wc_retailers_map', plugin_dir_url(__FILE__).'js/wc-retailers-map-admin.js', true );
  wp_enqueue_style('wc_retailers_map', plugin_dir_url(__FILE__).'css/wc-retailers-map-admin.css', true);


}

function wc_retailer_metabox_contact_cb($post, $metabox){
  $url = get_post_meta($post->ID, 'wc_retailer_map_url', true);
  $phone = get_post_meta($post->ID, 'wc_retailers_map_phone', true);
  ?>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_url" class="post-attributes-label"><?php echo __('Website'); ?></label></p>
  <input name="wc_retailer_map_url" value="<?php echo $url; ?>" />
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_phone" class="post-attributes-label"><?php echo __('Phone'); ?></label></p>
  <input name="wc_retailers_map_phone" value="<?php echo $phone; ?>" />
  <?php
}
function remove_yoast_metabox(){
  //remove_meta_box('wpseo_meta', 'retailer', 'normal');
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox',11 );


function wc_retailer_metabox_address_cb($post, $metabox){

  $address = get_post_meta($post->ID, 'wc_retailer_map_address', true);
  $city = get_post_meta($post->ID, 'wc_retailer_map_city', true);
  $state = get_post_meta($post->ID, 'wc_retailer_map_state', true);
  $country = get_post_meta($post->ID, 'wc_retailer_map_country', true);
  $postal_code = get_post_meta($post->ID, 'wc_retailer_map_postal', true);
  $country = get_post_meta($post->ID, 'wc_retailers_map_country', true);
  $lat = get_post_meta($post->ID, 'wc_retailers_map_lat', true);
  $long = get_post_meta($post->ID, 'wc_retailers_map_long', true);

  ?>
  <div ng-app="wcRetailersMap" ng-controller="wcRetailersMapController as ctrl">
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_address" class="post-attributes-label"><?php echo  __('Address'); ?></label></p>
    <input name="wc_retailer_map_address" value="<?php echo $address; ?>" ng-model="ctrl.address"/>
    <br/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_city" class="post-attributes-label"><?php echo __('City'); ?></label></p>
    <input name="wc_retailer_map_city" value="<?php echo $city; ?>" ng-model="ctrl.city"/>
    <br/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_state" class="post-attributes-label"><?php echo __('State'); ?></label></p>
    <input name="wc_retailer_map_state" value="<?php echo $state; ?>" ng-model="ctrl.state"/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailers_map_postal" class="post-attributes-label"><?php echo __('Postal Code'); ?></label></p>
    <input name="wc_retailers_map_postal" value="<?php echo $postal_code; ?>" ng-model="ctrl.postal_code"/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailers_map_country" class="post-attributes-label"><?php echo __('Country'); ?></label></p>
    <input name="wc_retailers_map_country" value="<?php echo $country; ?>" ng-model="ctrl.country"/>
    <p class="post-attributes-label-wrapper"></p>
    <br/>
    <br/>
    <button class="button" type="button" ng-click="ctrl.geocodeLocation()"><?php echo __('Geocode'); ?></button>
    <p class="post-attributes-label-wrapper"><label for="wc_retailers_map_lat" class="post-attributes-label"><?php echo __('Latitude'); ?></label></p>
    <input name="wc_retailers_map_lat" value="<?php echo $lat; ?>" ng-model="ctrl.lat" />
    <p class="post-attributes-label-wrapper"><label for="wc_retailers_map_long" class="post-attributes-label"><?php echo __('Longitude'); ?></label></p>
    <input name="wc_retailers_map_long" value="<?php echo $long; ?>" ng-model="ctrl.long" />
  </div>
  <?php
}

function wc_retailer_map_uninstall(){

}
register_activation_hook(__FILE__, 'wc_retailer_map_install');
register_uninstall_hook(__FILE__, 'wc_retailer_map_uninstall');


add_action('admin_menu', 'wc_retailer_admin_page');
