<?php
/*
Plugin Name: Bangladeshi Bank Payment Method
Plugin URI:  https://raisul.dev/projects/bangladeshi-bank-payment-method-for-woocommerce-plugin
Description: WooCommerce payment gateway for Bangladeshi businesses that allows customers to upload a bank payment receipt (screenshot/image) during checkout for manual verification.
Version:     1.1.7
Author:      Raisul Islam Shagor
Author URI:  https://raisul.dev
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Requires Plugins: woocommerce
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Contributors: shagor447
Text Domain: bangladeshi-bank-payment-method
Domain Path: /languages
Stable tag:  1.1.7
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

    add_action( 'wp_enqueue_scripts', function() {
        if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
            
            /**
             * Using anonymous logic inside action to avoid Non-Prefixed Function Warnings
             */
            $css_path = plugin_dir_path( __FILE__ ) . 'assets/bbpm-style-checkout.css';
            $js_path  = plugin_dir_path( __FILE__ ) . 'assets/bbpm-checkout.js';
            
            $css_v = file_exists( $css_path ) ? filemtime( $css_path ) : '1.1.7';
            $js_v  = file_exists( $js_path ) ? filemtime( $js_path ) : '1.1.7';

            wp_enqueue_style( 'bbpm-style-checkout', plugins_url( 'assets/bbpm-style-checkout.css', __FILE__ ), array(), $css_v );
            wp_enqueue_script( 'bbpm-checkout-js', plugins_url( 'assets/bbpm-checkout.js', __FILE__ ), array( 'jquery' ), $js_v, true );

            wp_localize_script( 'bbpm-checkout-js', 'BBPM_Data', array(
                'gatewayId'      => 'bangladeshi_bank_payment',
                'fileSizeError'  => __( 'File size exceeds 5MB limit.', 'bangladeshi-bank-payment-method' ),
                'fileInputSelector' => '#bangladeshi_bank_payment-receipt'
            ));
        }
    });

    add_filter( 'woocommerce_payment_gateways', function( $methods ) {
        $methods[] = 'RSLDVBBPM_WC_Gateway_Bangladeshi_Bank_Payment';
        return $methods;
    });

    add_action( 'plugins_loaded', function() {
        if ( class_exists( 'WC_Payment_Gateway' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-rsldvbbpm-wc-gateway-bangladeshi-bank-payment.php';
        }
    }, 11 );
}
