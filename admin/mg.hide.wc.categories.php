<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//main
function mg_hide_wc_categories_dashboard(){
  ?>
  <h1>Hide Shop Categories for Woocommerce</h1>
  <h4><i>You don't need to delete categories anymore to hide them from your shop!</i></h4>
  <p>Features</p>
  <ul>
    <li>
      - Hide products categories/subcategories with a single click directly from the Product->Categories page
    </li>
    <li>
      - Return 404 page for the category page of the hidden category
    </li>
  </ul>
  <h3>How to use</h3>
  <p>Usage of Hide Shop Categories for Woocommerce is very simple.</p>
  <ul>
    <li>- open Products->Categories from the admin menu</li>
    <li>- for each category you will have a column named Disabled</li>
    <li>- click on the checkbox of the Hide column to hide/show the category</li>
  </ul>
  <p>
    <i>Note: <strong>when the checkbox is marked means category is hidden</strong></i>
  </p>
  <p>
    <i>Updating of the hide/show status of the category is performed thru standard WP Ajax functions. Wait until the animated indicator disappear in order to hide/show another category</i>
  </p>
  <p>When a category is hidden products are not affected and still available in your product list.</p>
  <p>In this way you can easily hide categories from the shop without affecting your product database</p>
  <p>
    <small>Hide Shop Categories for Woocommerce &copy; <?php echo date('Y');?> - Antonio Nardone</small>
  </p>
<?php
}
?>
