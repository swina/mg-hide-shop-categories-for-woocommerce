<?php
/**
* Plugin Name: Moodgiver Hide Shop Categories
* Plugin URI: https://github.com/swina/mg-hide-shop-categories-for-woocommerce
* Description: Add hide flag to categories and disable from the standard categories menu
* Version: 1.0
* Author: Antonio Nardone
* Author URI: https://antonionardone.com
* License: GPL3
* Date: december 2017
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

  /* required files */
  /* main class functions */
  require_once dirname( __FILE__ ) . '/include/moodgiver.class.hide.wc.categories.php';
  /* plugin dashboard */
  require_once dirname( __FILE__ ) . '/admin/mg.hide.wc.categories.php';

  /* custom fields callback */
  function mood_hide_categories_woocommerce_manager(){
    mood_hide_categories_woocommerce();
  }


function moodgiver_hide_wc_categories_load_plugin_textdomain() {
  load_plugin_textdomain( 'mood_hide_wc_categories', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'moodgiver_hide_wc_categories_load_plugin_textdomain' );


/*load js, css and bootstrap modal*/
function mood_hide_categories_woocommerce_plugin_assets() {
  wp_register_style ( 'hidecss' , plugin_dir_url( __FILE__ ) . 'woo.hide.css', '' , '', 'all' );
  wp_register_script ( 'hidejs' , plugin_dir_url( __FILE__ ) . 'woo.hide.categories.js', array( 'jquery' ), '1', true );
  wp_enqueue_script( 'hidejs' );
  wp_localize_script( 'hidejs', 'wp_hide_category_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
  wp_enqueue_style( 'hidecss' );
}

//action load boostrap and plugin jss / css
add_action('admin_enqueue_scripts', 'mood_hide_categories_woocommerce_plugin_assets');
