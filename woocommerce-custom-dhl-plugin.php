<?php
/*
Plugin Name: WooCommerce Custom DHL Plugin
Description: Custom DHL shipping integration for WooCommerce.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize the plugin.
 */
function custom_dhl_plugin_init() {
    // Check if WooCommerce is active
    if (class_exists('WooCommerce')) {
        // Include necessary files
        require_once(plugin_dir_path(__FILE__) . 'includes/class-dhl.php');
		require_once(plugin_dir_path(__FILE__) . 'includes/custom-dhl-express-shipping.php');
		//require_once(plugin_dir_path(__FILE__) . 'includes/custom_free_shipping.php');
		require_once(plugin_dir_path(__FILE__) . 'includes/custom_vendo_field.php');
        // Add the custom DHL shipping method
        add_filter('woocommerce_shipping_methods', 'add_custom_dhl_shipping_method');
        function add_custom_dhl_shipping_method($methods) {
            $methods['custom_dhl_express_shipping'] = 'Custom_DHL_Express_Shipping_Method';
            return $methods;
        }
    } else {
        // WooCommerce is not active, so display a notice
        add_action('admin_notices', 'custom_dhl_plugin_missing_wc_notice');
        function custom_dhl_plugin_missing_wc_notice() {
            echo '<div class="error"><p>';
            echo 'WooCommerce Custom DHL Plugin requires WooCommerce to be installed and activated.';
            echo '</p></div>';
        }
    }
}
add_action('plugins_loaded', 'custom_dhl_plugin_init');
/*add_filter('woocommerce_add_cart_item_data', 'store_product_dimensions_in_cart', 10, 3);

function store_product_dimensions_in_cart($cart_item_data, $product_id, $variation_id) {
    // Get product dimensions
    $length = get_field('product_length', $product_id);
    $width = get_field('product_width', $product_id);
    $height = get_field('product_height', $product_id);

    // Store dimensions in cart item data
    if (!empty($length) && !empty($width) && !empty($height)) {
        $cart_item_data['product_dimensions'] = array(
            'length' => $length,
            'width' => $width,
            'height' => $height
        );
    }

    return $cart_item_data;
}*/