<?php

/* Custom Tabs & Fields for Woocommerce by moodgiver */
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('mood_hide_categories_woocommerce') ):

	//plugin master class
	class mood_hide_categories_woocommerce {
    public function __construct(){
			add_action('admin_menu', array( $this, 'mg_hide_category_admin_menu' ) );
      add_action('product_cat_add_form_fields', array( $this , 'mg_hide_category_taxonomy_add_new_meta_field'), 10, 1);
      add_action('product_cat_edit_form_fields', array( $this , 'mg_hide_category_taxonomy_edit_meta_field'), 10, 1);
      add_action('edited_product_cat', array ( $this , 'mg_hide_category_save_taxonomy_custom_meta' ), 10, 1);
      add_action('create_product_cat', array ( $this , 'mg_hide_category_save_taxonomy_custom_meta' ), 10, 1);
      add_filter( 'manage_edit-product_cat_columns', array ( $this , 'mg_hide_category_customFieldsListTitle' ));
      add_action( 'manage_product_cat_custom_column', array ( $this , 'mg_hide_category_customFieldsListDisplay') , 10, 3);
      add_action( 'wp_ajax_hide_category_woo', array ( $this , 'mg_hide_category_ajax_hide_category_woocommerce') ,10 , 1);
      add_filter( 'get_terms', array($this,'mg_hide_category_get_subcategory_terms'), 10, 3 );
      add_action( 'woocommerce_archive_description', array ( $this , 'mg_hide_category_check_category_disabled'),10,1);
    }


		//Add dashboard to options page
		function mg_hide_category_admin_menu() {
			add_management_page(
				'Hide Shop Categories for Woocommerce',
				'Woo Hide Categories',
				'manage_options',
				'mg_hide_wc_categories',
				'mg_hide_wc_categories_dashboard'
			);
		}

    //Product Cat Create page
    function mg_hide_category_taxonomy_add_new_meta_field() {
        ?>
        <div class="form-field">
            <label for="wh_meta_hide"><?php _e('Hide', 'mood_hide_wc_categories'); ?></label>
            <input type="checkbox" name="wh_meta_hide" id="wh_meta_hide">
            <p class="description"><?php _e('Hide category', 'mood_hide_wc_categories'); ?></p>
        </div>
        <?php
    }

    //Product Cat Edit page
    function mg_hide_category_taxonomy_edit_meta_field($term) {
        //getting term ID
        $term_id = $term->term_id;
        // retrieve the existing value(s) for this meta field.
        $wh_meta_hide = get_term_meta($term_id, 'wh_meta_hide', true);
        echo $wh_meta_hide;
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_hide"><?php _e('Hide category', 'mood_hide_wc_categories'); ?></label></th>
            <td>
                <?php
                $checked = '';
                if ( $wh_meta_hide ){ $checked = 'checked'; }
                ?>
                <input type="checkbox" name="wh_meta_hide" id="wh_meta_hide" <?php echo $checked;?>>
                <p class="description"><?php _e('Hide category', 'mood_hide_wc_categories'); ?></p>
            </td>
        </tr>
        <?php
    }

    // Save extra taxonomy fields callback function.
    function mg_hide_category_save_taxonomy_custom_meta($term_id) {
        $wh_meta_hide = filter_input(INPUT_POST, 'wh_meta_hide');
        update_term_meta($term_id, 'wh_meta_hide', $wh_meta_hide);
    }

    /**
     * Hide column added to category admin screen.
     *
     * @param mixed $columns
     * @return array
     */
    function mg_hide_category_customFieldsListTitle( $columns ) {
        $columns['pro_meta_hide'] = __( 'Hide', 'woocommerce' );
        return $columns;
    }
    /**
     * Hide column checkbox to product category admin screen.
     *
     * @param string $columns
     * @param string $column
     * @param int $id term ID
     *
     * @return string
     */
    function mg_hide_category_customFieldsListDisplay( $columns, $column, $id ) {
        if ( 'pro_meta_hide' == $column ) {
            $isDisabled = get_term_meta($id,'wh_meta_hide',true);
            $checked = '';
            if ( $isDisabled == 'on' ){ $checked = 'checked';}
            $columns = '<input type="checkbox" class="mood_hide_category" name="wh_meta_hide" id="wh_meta_hide" data-id="'.$id.'" '.$checked.'><small class="working working_'.$id.'"></small>';
            //$columns = esc_html( get_term_meta($id, 'wh_meta_hide', true) );
        }
        return $columns;
    }

    function mg_hide_category_ajax_hide_category_woocommerce(){
      update_term_meta(filter_input(INPUT_POST,$_POST['id']), 'wh_meta_hide', filter_input(INPUT_POST,$_POST['checked']));
    }

    /**
     * Show products only of enabled categories.
     */
    function mg_hide_category_get_subcategory_terms( $terms, $taxonomies, $args ) {

    	$new_terms 	= array();
      $sorted_menu = array();
     	// if a product category and on the shop page
    	if ( in_array( 'product_cat', $taxonomies ) && !is_admin() && (is_shop() || class_exists('Woocommerce'))  ) {
    	    foreach ( $terms as $key => $term ) {
           if ( get_term_meta($term->term_id, 'wh_meta_hide',true) != 'on' ){
    			    $new_terms[] = $term;
              $sorted_menu[$key] = get_term_meta($term->term_id,'order',true);
    		    }
    	    }
          array_multisort( $sorted_menu, SORT_ASC, $new_terms );
    	    $terms = $new_terms;
    	}

     return $terms;
    }

    function mg_hide_category_check_category_disabled(){
      if ( is_product_category() ){
  	    global $wp_query;
  	    $cat = $wp_query->get_queried_object();
        if ( get_term_meta($cat->term_id, 'wh_meta_hide',true) == 'on' ){
          echo '<style>.page-title { display:none; }</style>';
          $wp_query->set_404();
          status_header( 404 );
          get_template_part( 404 );
          exit();
        }
      }
    }

  }
endif;
new mood_hide_categories_woocommerce();
