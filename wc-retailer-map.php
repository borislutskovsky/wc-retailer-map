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
  /* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'wc_retailers_save_post_class_meta', 10, 2 );


function wc_retailers_save_post_class_meta($post_id, $post, $updating){

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['wc_retailer_post_nonce'] ) || !wp_verify_nonce( $_POST['wc_retailer_post_nonce'], basename( __FILE__ ) ) ) {
    return $post_id;
  }

    /* Get the post type object. */
  $post_type = get_post_type( $post_id );

  if($post_type != 'retailer') return $post_id;
  $post_type_object = get_post_type_object($post_type);
   /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type_object->cap->edit_post, $post_id ) ) {
    return $post_id;
  }


  //save address
  $new_address = (isset($_POST['wc_retailer_map_address']) ? sanitize_text_field($_POST['wc_retailer_map_address']) : '');
  $address =  get_post_meta($post_id, 'wc_retailer_map_address', true);

  if($new_address && $address == ''){
    add_post_meta($post_id, 'wc_retailer_map_address', $new_address, true);
  } else if($new_address && $address != $new_address){
    update_post_meta($post_id, 'wc_retailer_map_address', $new_address);
  } else if($new_address == '' && $address){
    delete_post_meta($post_id, 'wc_retailer_map_address', $address);
  }

  //save address2
  $new_address2 = (isset($_POST['wc_retailer_map_address2']) ? sanitize_text_field($_POST['wc_retailer_map_address2']) : '');
  $address2 =  get_post_meta($post_id, 'wc_retailer_map_address2', true);

  if($new_address2 && $address2 == ''){
    add_post_meta($post_id, 'wc_retailer_map_address2', $new_address2, true);
  } else if($new_address2 && $address2 != $new_address2){
    update_post_meta($post_id, 'wc_retailer_map_address2', $new_address2);
  } else if($new_address2 == '' && $address2){
    delete_post_meta($post_id, 'wc_retailer_map_address2', $address2);
  }

  //city
  $new_city = (isset($_POST['wc_retailer_map_city']) ? sanitize_text_field($_POST['wc_retailer_map_city']) : '');
  $city = get_post_meta($post_id, 'wc_retailer_map_city', true);
  if($new_city && $city == ''){
    add_post_meta($post_id, 'wc_retailer_map_city', $new_city, true);
  } else if($new_city && $city != $new_city){
    update_post_meta($post_id, 'wc_retailer_map_city', $new_city);
  } else if($new_city == '' && $city){
    delete_post_meta($post_id, 'wc_retailer_map_city', $city);
  }

  //save state
  $new_state = (isset($_POST['wc_retailer_map_state']) ? sanitize_text_field($_POST['wc_retailer_map_state']) : '');
  $state =  get_post_meta($post_id, 'wc_retailer_map_state', true);

  if($new_state && $state == ''){
    add_post_meta($post_id, 'wc_retailer_map_state', $new_state, true);
  } else if($new_state && $state != $new_state){
    update_post_meta($post_id, 'wc_retailer_map_state', $new_state);
  } else if($new_state == '' && $state){
    delete_post_meta($post_id, 'wc_retailer_map_state', $state);
  }

  //save postal_code
  $new_postal_code = (isset($_POST['wc_retailer_map_postal_code']) ? sanitize_text_field($_POST['wc_retailer_map_postal_code']) : '');
  $postal_code =  get_post_meta($post_id, 'wc_retailer_map_postal_code', true);

  if($new_postal_code && $postal_code == ''){
    add_post_meta($post_id, 'wc_retailer_map_postal_code', $new_postal_code, true);
  } else if($new_postal_code && $postal_code != $new_postal_code){
    update_post_meta($post_id, 'wc_retailer_map_postal_code', $new_postal_code);
  } else if($new_postal_code == '' && $postal_code){
    delete_post_meta($post_id, 'wc_retailer_map_postal_code', $postal_code);
  }

  //save country
  $new_country = (isset($_POST['wc_retailer_map_country']) ? sanitize_text_field($_POST['wc_retailer_map_country']) : '');
  $country =  get_post_meta($post_id, 'wc_retailer_map_country', true);

  if($new_country && $country == ''){
    add_post_meta($post_id, 'wc_retailer_map_country', $new_country, true);
  } else if($new_country && $country != $new_country){
    update_post_meta($post_id, 'wc_retailer_map_country', $new_country);
  } else if($new_country == '' && $country){
    delete_post_meta($post_id, 'wc_retailer_map_address', $country);
  }

  //save lat
  $new_lat = (isset($_POST['wc_retailer_map_lat']) ? sanitize_text_field($_POST['wc_retailer_map_lat']) : '');
  $lat =  get_post_meta($post_id, 'wc_retailer_map_lat', true);

  if($new_lat && $lat == ''){
    add_post_meta($post_id, 'wc_retailer_map_lat', $new_lat, true);
  } else if($new_lat && $lat != $new_lat){
    update_post_meta($post_id, 'wc_retailer_map_lat', $new_lat);
  } else if($new_lat == '' && $lat){
    delete_post_meta($post_id, 'wc_retailer_map_lat', $lat);
  }

  //save long
  $new_long = (isset($_POST['wc_retailer_map_long']) ? sanitize_text_field($_POST['wc_retailer_map_long']) : '');
  $long =  get_post_meta($post_id, 'wc_retailer_map_long', true);

  if($new_long && $long == ''){
    add_post_meta($post_id, 'wc_retailer_map_long', $new_long, true);
  } else if($new_long && $long != $new_long){
    update_post_meta($post_id, 'wc_retailer_map_long', $new_long);
  } else if($new_long == '' && $long){
    delete_post_meta($post_id, 'wc_retailer_map_long', $long);
  }

}

function wc_retailer_metabox_contact_cb($post, $metabox){
  $url = get_post_meta($post->ID, 'wc_retailer_map_url', true);
  $phone = get_post_meta($post->ID, 'wc_retailers_map_phone', true);
  ?>
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_url" class="post-attributes-label"><?php echo __('Website'); ?></label></p>
  <input name="wc_retailer_map_url" value="<?php echo $url; ?>" />
  <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_phone" class="post-attributes-label"><?php echo __('Phone'); ?></label></p>
  <input name="wc_retailer_map_phone" value="<?php echo $phone; ?>" />
  <?php
}
function remove_yoast_metabox(){
  //remove_meta_box('wpseo_meta', 'retailer', 'normal');
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox',11 );



function wc_retailer_metabox_address_cb($post, $metabox){

  $address = get_post_meta($post->ID, 'wc_retailer_map_address', true);
  $address2 = get_post_meta($post->ID, 'wc_retailer_map_address2', true);
  $city = get_post_meta($post->ID, 'wc_retailer_map_city', true);
  $state = get_post_meta($post->ID, 'wc_retailer_map_state', true);
  $country = get_post_meta($post->ID, 'wc_retailer_map_country', true);
  $postal_code = get_post_meta($post->ID, 'wc_retailer_map_postal_code', true);
  $country = get_post_meta($post->ID, 'wc_retailer_map_country', true);
  $lat = get_post_meta($post->ID, 'wc_retailer_map_lat', true);
  $long = get_post_meta($post->ID, 'wc_retailer_map_long', true);

  ?>
  <?php echo wp_nonce_field( basename( __FILE__ ), 'wc_retailer_post_nonce' ); ?>

  <div ng-app="wcRetailersMap" ng-controller="wcRetailersMapController as ctrl">
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_address" class="post-attributes-label"><?php echo  __('Address'); ?></label></p>
    <input name="wc_retailer_map_address" ng-init="ctrl.address='<?php echo $address; ?>'" ng-model="ctrl.address"/>
    <br/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_address2" class="post-attributes-label"></label></p>
    <input name="wc_retailer_map_address2" ng-init="ctrl.address2='<?php echo $address2; ?>'" ng-model="ctrl.address2" />
    <br/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_city" class="post-attributes-label"><?php echo __('City'); ?></label></p>
    <input name="wc_retailer_map_city" ng-init="ctrl.city='<?php echo $city; ?>'" ng-model="ctrl.city"/>
    <br/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_state" class="post-attributes-label"><?php echo __('State'); ?></label></p>
    <input name="wc_retailer_map_state" ng-init="ctrl.state='<?php echo $state; ?>'" ng-model="ctrl.state"/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_postal" class="post-attributes-label"><?php echo __('Postal Code'); ?></label></p>
    <input name="wc_retailer_map_postal_code" ng-init="ctrl.postal_code='<?php echo $postal_code; ?>'" ng-model="ctrl.postal_code"/>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_country" class="post-attributes-label"><?php echo __('Country'); ?></label></p>
    <input name="wc_retailer_map_country" ng-init="ctrl.country='<?php echo $country; ?>'" ng-model="ctrl.country"/>
    <p class="post-attribute-label-wrapper"></p>
    <br/>
    <br/>
    <button class="button" type="button" ng-click="ctrl.geocodeLocation()"><?php echo __('Geocode'); ?></button>
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_lat" class="post-attributes-label"><?php echo __('Latitude'); ?></label></p>
    <input name="wc_retailer_map_lat" ng-init="ctrl.lat='<?php echo $lat; ?>'" ng-model="ctrl.lat" />
    <p class="post-attributes-label-wrapper"><label for="wc_retailer_map_long" class="post-attributes-label"><?php echo __('Longitude'); ?></label></p>
    <input name="wc_retailer_map_long" ng-init="ctrl.long='<?php echo $long; ?>'" ng-model="ctrl.long" />
  </div>
  <?php
}

function wc_retailer_map_uninstall(){

}
register_activation_hook(__FILE__, 'wc_retailer_map_install');
register_uninstall_hook(__FILE__, 'wc_retailer_map_uninstall');


add_action('admin_menu', 'wc_retailer_admin_page');
