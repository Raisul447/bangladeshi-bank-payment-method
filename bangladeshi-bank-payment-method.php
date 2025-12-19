<?php
/*
Plugin Name: Bangladeshi Bank Payment Method
Plugin URI:  https://raisul.dev/projects/bangladeshi-bank-payment-method-for-woocommerce-plugin
Description: WooCommerce payment gateway for Bangladeshi businesses that allows customers to upload a bank payment receipt (screenshot/image) during checkout for manual verification.
Version:     1.0.6
Author:      Raisul Islam Shagor
Author URI: https://raisul.dev
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Requires Plugins: woocommerce
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Contributors: shagor447
Text Domain: bangladeshi-bank-payment-method
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active (FIXED Naming Convention)
 **/

// **CRITICAL FIX:** Ensure the is_plugin_active function is available before calling it
if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Checking for WooCommerce activation using the correct standard function
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

    // Global function prefixed
    function rsldvbbpm_enqueue_styles() {
        if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
            wp_enqueue_style( 'bbpm-style', plugins_url( 'assets/bbpm-styles.css', __FILE__ ), array(), '1.0.1' );
        }
    }
    add_action( 'wp_enqueue_scripts', 'rsldvbbpm_enqueue_styles' );

    /**
     * Enqueue custom JS for non-AJAX file upload and localize data
     */
    // Global function prefixed
    function rsldvbbpm_enqueue_scripts() {
        if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
            wp_enqueue_script(
                'bbpm-checkout-js',
                plugins_url( 'assets/bbpm-checkout.js', __FILE__ ),
                array( 'jquery', 'wc-checkout' ),
                '1.0.5',
                true
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'rsldvbbpm_enqueue_scripts', 20 );

    
    // Global function prefixed 
    function rsldvbbpm_add_gateway_class( $methods ) {
        // Updated Class Name
        $methods[] = 'RSLDVBBPM_WC_Gateway_Bangladeshi_Bank_Payment';
        return $methods;
    }
    add_filter( 'woocommerce_payment_gateways', 'rsldvbbpm_add_gateway_class' );

    // Global function prefixed
    function rsldvbbpm_woocommerce_bank_gateway_init() {
        if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        // Updated required file path
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-rsldvbbpm-wc-gateway-bangladeshi-bank-payment.php';
    }
    add_action( 'plugins_loaded', 'rsldvbbpm_woocommerce_bank_gateway_init', 11 );
}
