<?php

/*

Plugin Name: C55 Unleahsed to woocommerce sync

Description: Sync product data from woocommerce to Unleashed.

Version: 1.0.0

Author: Mark Dowton

Author URI: https://www.linkedin.com/in/mark-dowton-03a85a41/

Text Domain: Unleashed to woocommerce

*/

include_once('src/services/http-services.php');
include_once('src/services/unleashed-services/unleashed-stock.php');
include_once('src/helpers/helpers.php');
include_once('src/helpers/c55_create_variable_product.php');
include_once('src/all-products/c55-all-products.php');
include_once('src/stock-adjustments/c55-stock-adjustments.php');
include_once('src/woocommerce-products/c55-woocommerce-products.php');
include_once('src/woocommerce-products/woocommerce_product_hooks.php');


add_action('admin_menu', 'my_admin_menu');
add_action('admin_enqueue_scripts', 'register_my_plugin_scripts');
add_action('admin_enqueue_scripts', 'load_my_plugin_scripts');
add_action('admin_init', 'my_settings_init');
// Hook for CRON
add_action( 'unleashed_cron_hook', 'unleashed_cron_exec' );



const PRODUCT_GROUP = 'retail';

function unleashed_cron_exec() {
    // Calls the main loop to execute
    my_setting_section_callback_function();
}

function my_admin_menu()
{
    add_menu_page(
        __('Sample page', 'my-textdomain'),
        __('Woo-Unleashed', 'my-textdomain'),
        'manage_options',
        'sample-page',
        'my_admin_page_contents',
        'dashicons-schedule',
        3
    );
}


function my_admin_page_contents()
{
?>
    <form method="POST" action="options.php">
        <?php
        // my_setting_markup();
        settings_fields('sample-page');
        do_settings_sections('sample-page');
        submit_button();
        ?>
    </form>
<?php
}


function register_my_plugin_scripts()
{
    wp_register_style('my-plugin', plugins_url('./c55-woocommerce-unleashed/assest/css/styles.css'));
    // wp_register_script('my-plugin', plugins_url('ddd/js/plugin.js' ) );
}

function load_my_plugin_scripts($hook)
{
    // Load only on ?page=sample-page
    if ($hook != 'toplevel_page_sample-page') {
        return;
    }
    // Load style & scripts.
    wp_enqueue_style('my-plugin');
    wp_enqueue_script('my-plugin');
}
function my_settings_init()
{

    add_settings_section(
        'sample_page_setting_section',
        __('Unleashed API Settings', 'my-textdomain'),
        'my_setting_section_callback_function',
        'sample-page'
    );

    add_settings_field(
        'unleashed_api_id',
        __('API Credentials :', 'my-textdomain'),
        'my_setting_markup',
        'sample-page',
        'sample_page_setting_section'
    );

    register_setting('sample-page', 'unleashed_api_id');
}

// MAIN LOOP
function my_setting_section_callback_function()
{
    
    $model = c55_syncAllProducts();

    if ($model) {
        if ($model['Pagination'] && (int) $model['Pagination']['NumberOfPages'] > 1) {
            foreach ($model['Pagination'] as $page) {
                // Need to pass a page param for second call
                if ((int)$page === 1) {
                    c55_loop_product_items($model['Items']);
                    break;
                }
                $nextPage = (int) $model['Pagination']['NumberOfPages'] + 1;
                $model = c55_syncAllProducts($nextPage);
                c55_loop_product_items($model['Items']);
            }
        } else {
            // Single page of results
            c55_loop_product_items($model);
        }
    }
}

function c55_loop_product_items($model)
{
    foreach ($model['Items'] as $key => $item) {
        // If they are variation product ....
        if ($item && !empty($item['AttributeSet'])) {
            $attributes = [];
            $parentVariation = '';
            foreach ($item['AttributeSet']['Attributes'] as $key => $attr) {
                if ($attr['Name'] === 'Name') {
                    $parentVariation = strtolower($attr['Value']);
                } else {
                    $attributes[strtolower($attr['Name'])] = [$attr['Value']];
                }
            }

            // Upload the product image
            $attachId = null;
            if (isset($item['ImageUrl'])) {
                // $fileName = preg_replace("/[\s_]/", "-", $item['ProductDescription']);
                // $attachId = c55_upload_product_image($url, $fileName . '.jpg');
                $attachId = $item['ImageUrl'];
            }

            // Check if SKU already loaded
            $sku = str_replace(" ", "-", $parentVariation);
            $isFound = c55_get_product_by_sku($sku);
            // If not found create tthe variable product
            if ((int)$isFound === 0) {
                $productId = create_product_variable(array(
                    'author'        => '', // optional
                    'title'         => $parentVariation,
                    'content'       => $item['ProductDescription'],
                    'excerpt'       => $item['ProductDescription'],
                    'regular_price' => '', // product regular price
                    'sale_price'    => '', // product sale price (optional)
                    'stock'         => '', // Set a minimal stock quantity
                    'set_manage_stock' => false,
                    'image_id'      => '', // optional
                    'gallery_ids'   => array(), // optional
                    'sku'           => $sku, // optional
                    'tax_class'     => '', // optional
                    'weight'        => '', // optional
                    // For NEW attributes/values use NAMES (not slugs)
                    'attributes'    => $attributes
                ));
                // create the variations
                $parent_id = $productId; // Or get the variable product id dynamically
            } else {
                echo 'Varibale product exists update <br/>';
                c55_updateProductVariable($isFound, $item);
            }
            // Else add tha variations.
            // The variation data
            $variationAtrr = [];
            foreach ($item['AttributeSet']['Attributes'] as $key => $attr) {
                if ($attr['Name'] !== 'Name') {
                    $variationAtrr[strtolower($attr['Name'])] = $attr['Value'];
                }
            }
            // Call to get stock levels by GUID
            $stock_qty = c55_getStockOnHand($item['Guid']);
            $isFoundVariation = c55_get_product_by_sku($item['ProductCode']);

            if ((int)$isFoundVariation === 0) {
                echo 'Creating a variant <br/>';
                $variation_data =  array(
                    'attributes' => $variationAtrr,
                    'sku'           => $item['ProductCode'],
                    'regular_price' => $item['SellPriceTier1']['Value'],
                    'sale_price'    => '',
                    'stock_qty'     => $stock_qty,
                    'image' => $attachId
                );
                if ((int)$isFound !== 0) {
                    $parent_id = $isFound;
                }
                create_product_variations($parent_id, $variation_data);
                c55_updateDefaultAttributes($parent_id);
            } else {
                echo 'variant exist update <br/>';
            }
        }
    }
    echo 'Uploaded complete ....';
}
function my_setting_markup()
{
?>
    <section id="section-form">
        <div style="padding: 0 20px 20px 20px;">
            <label for="my-input"><?php _e('Unleashed API ID'); ?>:</label>
            <input style="min-width: 600px;" type="text" id="unleashed_api_id" name="unleashed_api_id" value="<?php echo get_option('unleashed_api_id'); ?>">
        </div>
        <div style="padding: 0 20px 20px 20px;">
            <label for="my-input"><?php _e('Unleashed API Key'); ?>:</label>
            <input style="min-width: 600px;" type="text" id="unleashed_api_key" name="unleashed_api_key" value="<?php echo get_option('unleashed_api_key'); ?>">
        </div>
        <div style="padding: 0 20px 20px 20px;">
            <button id="unleashed_sync" name="unleashed_sync" class="button button-primary">Sync unleashed to woo-comm</button>
        </div>
    </section>
<?php
}
