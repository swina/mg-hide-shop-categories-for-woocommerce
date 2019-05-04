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
            add_action('template_redirect',array ( $this , 'mg_redirect_single_product_page_when_all_categories_hidden'),10,1);
			add_action( 'pre_get_posts', array ( $this ,'mg_exclude_products_from_results_when_all_categories_are_hidden'),10,1 );
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
    <label for="wh_meta_hide">
        <?php _e('Hide', 'mood_hide_wc_categories'); ?>
    </label>
    <input type="checkbox" name="wh_meta_hide" id="wh_meta_hide" />
    <p class="description">
        <?php _e('Hide category', 'mood_hide_wc_categories'); ?>
    </p>
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
    <th scope="row" valign="top">
        <label for="wh_meta_hide">
            <?php _e('Hide category', 'mood_hide_wc_categories'); ?>
        </label>
    </th>
    <td>
        <?php
            $checked = '';
            if ( $wh_meta_hide ){ $checked = 'checked'; }
        ?>
        <input type="checkbox" name="wh_meta_hide" id="wh_meta_hide" <?php echo $checked;?> />
        <p class="description">
            <?php _e('Hide category', 'mood_hide_wc_categories'); ?>
        </p>
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
            update_term_meta(filter_input(INPUT_POST,$_POST['id'],FILTER_VALIDATE_INT), 'wh_meta_hide', filter_input(INPUT_POST,$_POST['checked'],FILTER_VALIDATE_BOOLEAN));
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
                    if(isset($term->term_id)){
                        if ( get_term_meta($term->term_id, 'wh_meta_hide',true) != 'on' ){
                            $new_terms[] = $term;
                            $sorted_menu[$key] = get_term_meta($term->term_id,'order',true);
                        }
                    }
                    array_multisort( $sorted_menu, SORT_ASC, $new_terms );
                    $terms = $new_terms;
                }
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
        // block access to single product pages that do not have any categories associated (removed by Hide) or if the only category is Uncategorized (default when you delete all categories).
        // it might be most appropriate to add Uncategorized back to the product_cat array if you end up with an empty array after removing hidden categories?
        function mg_redirect_single_product_page_when_all_categories_hidden() {
            if ( !is_admin() && (is_shop() || class_exists('Woocommerce'))  ) {
                if ( is_product() ){
                    $queried_object = get_queried_object();
                    $terms = get_the_terms (  $queried_object->ID, 'product_cat' );
                    $redirect=(bool)(!$terms);// empty array will redirect
                    if(!$redirect){
                        $redirect=(count($terms)==1 && $terms[0]->name=='Uncategorized');
                    }
                    if($redirect){
                        wp_redirect(site_url('/404'));
                    }
                }
            }
        }
		        function mg_exclude_products_from_results_when_all_categories_are_hidden( $q ) {
            if ( !is_admin() && (is_shop() || class_exists('Woocommerce'))  ) {
                if ( (isset($q->query['post_type']) && $q->query['post_type']=='product') || $q->is_search){
                    $tax_query = (array) $q->get( 'tax_query' );
                    $exclusions=$this->mg_get_product_cat_ids_to_exclude();
                    if($exclusions){
                        $inclusions=$this->mg_get_product_cat_ids_to_include();
                        $tax_query[] = array(
                            'relation' => 'OR',
                            array(
                                'taxonomy'  => 'product_cat',
                                'field'     => 'term_id',
                                'terms'     => $exclusions,
                                'operator'  => 'NOT IN'),
                              array(
                                 'taxonomy'  => 'product_cat',
                                 'field'     => 'term_id',
                                 'terms'     => $inclusions,
                                 'operator'  => 'IN'),
                             );
                        $q->set('tax_query',$tax_query);
                    }
                }
            }
        }
        // could add a plugin configuration page where you can define cache expiration, add a button to explicitly expire cache, and perhaps have an override for hiding products
        // meaning, if you want to hide products if ANY category is hidden, not just if ALL categories are hidden (noting that ALL is the current functionality)
        function mg_get_product_cat_ids_to_exclude(){
            if ( false === ( $mg_excluded_product_cats = get_transient( 'mg_excluded_product_cats' ) ) ) {
                global $wpdb;
                $mg_excluded_product_cats = $wpdb->get_col(
                    "select distinct tt.term_id " .
                    " from wp_term_relationships tr " .
                    " join wp_term_taxonomy tt on tr.term_taxonomy_id = tt.term_taxonomy_id " .
                    " join wp_termmeta tm on tt.term_id = tm.term_id " .
                    " where tt.taxonomy='product_cat' AND meta_key='wh_meta_hide' and meta_value='on' " );
                set_transient( 'mg_excluded_product_cats', $mg_excluded_product_cats, 60*60*1 ); // cache 60 seconds * 60 minutes * 1 hours .. should be a configurable value?
            }
            return $mg_excluded_product_cats;
        }
        function mg_get_product_cat_ids_to_include(){
            if ( false === ( $mg_included_product_cats = get_transient( 'mg_included_product_cats' ) ) ) {
                global $wpdb;
                $mg_included_product_cats = $wpdb->get_col(
                    "select distinct tt.term_id " .
                    " from wp_term_relationships tr " .
                    " join wp_term_taxonomy tt on tr.term_taxonomy_id = tt.term_taxonomy_id " .
                    " join wp_termmeta tm on tt.term_id = tm.term_id " .
                    " where tt.taxonomy='product_cat' AND meta_key='wh_meta_hide' and meta_value IS NULL " );
                set_transient( 'mg_included_product_cats', $mg_included_product_cats, 60*60*1 ); // cache 60 seconds * 60 minutes * 1 hours .. should be a configurable value?
            }
            return $mg_included_product_cats;
        }
    }
endif;
new mood_hide_categories_woocommerce();