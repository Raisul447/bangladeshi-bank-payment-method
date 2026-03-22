<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Class name updated with unique plugin prefix
class RSLDVBBPM_WC_Gateway_Bangladeshi_Bank_Payment extends WC_Payment_Gateway {

    public $account_name;
    public $account_number;
    public $account_holder;
    public $branch_name;
    public $routing_number;

    public function __construct() {
        $this->id                 = 'bangladeshi_bank_payment';
        $this->icon               = $this->get_bank_logo_url();
        $this->has_fields         = true;
        $this->method_title       = __( 'Bangladeshi Bank Payment Method', 'bangladeshi-bank-payment-method' );
        $this->method_description = __( 'Accept payments via direct bank transfer from customers in Bangladesh, with a bank receipt as proof of payment.', 'bangladeshi-bank-payment-method' );

        $this->init_form_fields();
        $this->init_settings();

        $this->title              = $this->get_option( 'title' );
        $this->description        = $this->get_option( 'description' );
        $this->account_name       = $this->get_option( 'account_name' );
        $this->account_number     = $this->get_option( 'account_number' );
        $this->account_holder     = $this->get_option( 'account_holder' );
        $this->branch_name        = $this->get_option( 'branch_name' );
        $this->routing_number     = $this->get_option( 'routing_number' );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_payment_receipt_admin_order' ) );
        add_filter( 'woocommerce_checkout_form_tag', array( $this, 'add_enctype_to_checkout_form' ), 99 );
        add_action( 'woocommerce_checkout_process', array( $this, 'custom_checkout_validation' ) );
    }

    public function add_enctype_to_checkout_form( $form_tag ) {
        if ( strpos( $form_tag, 'enctype=' ) === false ) {
            $form_tag = str_replace( '<form', '<form enctype="multipart/form-data"', $form_tag );
        }
        return $form_tag;
    }

    private function get_bank_logo_url() {
        $logo_url = $this->get_option( 'bank_logo_url' );
        return $logo_url ? $logo_url : plugin_dir_url( dirname( __FILE__ ) ) . 'assets/bb-bank-logo.png';
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'bangladeshi-bank-payment-method' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Bangladeshi Bank Payment', 'bangladeshi-bank-payment-method' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __( 'Title', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'description' => __( 'It will display as a title which the user sees during checkout.', 'bangladeshi-bank-payment-method' ),
                'default'     => __( 'Bangladeshi Bank Payment', 'bangladeshi-bank-payment-method' ),
                'desc_tip'    => true,
            ),
            'bank_settings_title' => array(
                'title'       => __( 'Bank Account Details', 'bangladeshi-bank-payment-method' ),
                'type'        => 'title',
                'description' => __( 'Enter the details of the bank account where customers should send payments.', 'bangladeshi-bank-payment-method' ),
            ),
            'account_name' => array(
                'title'       => __( 'Bank Account Name', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'default'     => 'Example Bank PLC',
                'placeholder' => 'Name of the Bank',
                'desc_tip'    => true,
            ),
            'account_number' => array(
                'title'       => __( 'Account Number', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'default'     => '1234567891011',
                'placeholder' => '1234567891011',
            ),
            'account_holder' => array(
                'title'       => __( 'Account Holder Name', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'default'     => 'Your Account Name',
                'placeholder' => 'Your Account Name',
            ),
            'branch_name' => array(
                'title'       => __( 'Branch Name', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'default'     => 'Dhaka Main Branch',
                'placeholder' => 'Dhaka Main Branch',
            ),
            'routing_number' => array(
                'title'       => __( 'Routing Number', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'default'     => '987654321',
                'placeholder' => '987654321',
            ),
            'bank_logo_url' => array(
                'title'       => __( 'Bank Logo URL', 'bangladeshi-bank-payment-method' ),
                'type'        => 'text',
                'description' => __( 'Enter the full URL of the bank logo (e.g., https://domain.com/assets/bank_logo.png) for the icon on the checkout page. The image should be small (e.g., 45x30px) for best display.', 'bangladeshi-bank-payment-method' ),
                'placeholder' => 'https://domain.com/assets/bank_logo.png',
                'desc_tip'    => true,
                'default'     => '',
                'custom_attributes' => array(
                    'style' => 'width: 400px; max-width: 100%;',
                ),
            ),
        );
    }

    public function payment_fields() {
        echo '<div id="bbpm-payment-details-wrapper" class="rsldv-container">';
            
            // Bank details card
            echo '<div class="rsldv-bank-card">';
                echo '<span class="rsldv-bank-header">' . esc_html( $this->account_name ) . '</span>';
                
                $details = array(
                    __( 'Account Number', 'bangladeshi-bank-payment-method' ) => $this->account_number,
                    __( 'Account Holder', 'bangladeshi-bank-payment-method' ) => $this->account_holder,
                    __( 'Branch Name', 'bangladeshi-bank-payment-method' )    => $this->branch_name,
                    __( 'Routing Number', 'bangladeshi-bank-payment-method' )  => $this->routing_number,
                );
    
                foreach ( $details as $label => $value ) {
                    echo '<div class="rsldv-info-row">';
                        echo '<span class="rsldv-label">' . esc_html( $label ) . ':</span>';
                        echo '<span class="rsldv-value">' . esc_html( $value ) . '</span>';
                    echo '</div>';
                }
            echo '</div>';
    
            // Upload field
            echo '<div class="rsldv-upload-section">';
                echo '<label class="rsldv-upload-label" for="' . esc_attr( $this->id ) . '-receipt">' . esc_html( __( 'Upload Payment Receipt (Required)', 'bangladeshi-bank-payment-method' ) ) . ' <span style="color:red;">*</span></label>';
                echo '<input id="' . esc_attr( $this->id ) . '-receipt" class="rsldv-file-input" type="file" name="bbpm_payment_receipt" accept="image/png,image/jpeg,image/jpg" required />';
                wp_nonce_field( 'bbpm_process_payment', 'bbpm_process_payment_nonce' );
                echo '<p style="font-size:12px; color:#64748b; margin-top:5px;">' . esc_html( __( 'Max: 5MB. Formats: PNG, JPG, JPEG.', 'bangladeshi-bank-payment-method' ) ) . '</p>';
            echo '</div>';
    
            // Help text alert
            echo '<div class="rsldv-alert-box">';
                echo '<p class="rsldv-alert-text">' . esc_html( __( 'Please pay the total amount through (NPSB) to avoid payment disruptions or delivery delays, and upload the payment receipt/screenshot to confirm your order.', 'bangladeshi-bank-payment-method' ) ) . '</p>';
            echo '</div>';
    
        echo '</div>';
    }

    public function custom_checkout_validation() {
        if ( isset( $_POST['payment_method'] ) && $_POST['payment_method'] === $this->id ) {
            if ( ! isset( $_POST['bbpm_process_payment_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bbpm_process_payment_nonce'] ), 'bbpm_process_payment' ) ) {
                wc_add_notice( __( 'Security check failed. Please try again.', 'bangladeshi-bank-payment-method' ), 'error' );
            }
        }
    }

    public function process_payment( $order_id ) {
        if ( empty( $_POST['bbpm_process_payment_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bbpm_process_payment_nonce'] ), 'bbpm_process_payment' ) ) {
            wc_add_notice( __( 'Security check failed. Please try again.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        if ( empty( $_FILES['bbpm_payment_receipt']['name'] ) ) {
            wc_add_notice( __( 'Please upload a payment receipt.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $file_data = $_FILES['bbpm_payment_receipt'];

        if ( ! function_exists( 'mime_content_type' ) ) {
            wc_add_notice( __( 'Server does not support file validation.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        $file_type = mime_content_type( $file_data['tmp_name'] );
        $allowed_types = array( 'image/jpeg', 'image/png' );
        if ( ! in_array( $file_type, $allowed_types, true ) ) {
            wc_add_notice( __( 'Invalid file type. Only JPG and PNG files are allowed.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        if ( $file_data['size'] > 5242880 ) {
            wc_add_notice( __( 'File size exceeds 5MB limit.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        add_filter( 'user_has_cap', function( $allcaps, $caps, $args, $user ) use ( $user_id ) {
            if ( $user->ID === $user_id && in_array( 'upload_files', $caps, true ) ) {
                $allcaps['upload_files'] = true;
            }
            return $allcaps;
        }, 10, 4 );

        add_filter( 'upload_size_limit', '__return_zero' );
        $uploaded_file = wp_handle_upload( $file_data, array( 'test_form' => false ) );
        remove_filter( 'upload_size_limit', '__return_zero' );

        if ( isset( $uploaded_file['error'] ) ) {
            /* translators: %s: Error message from file upload */
            wc_add_notice( sprintf( __( 'Upload failed: %s', 'bangladeshi-bank-payment-method' ), esc_html( $uploaded_file['error'] ) ), 'error' );
            return;
        }

        $attachment = array(
            'guid'           => $uploaded_file['url'],
            'post_mime_type' => $uploaded_file['type'],
            'post_title'     => sanitize_file_name( $file_data['name'] ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'], $order_id );
        if ( ! is_wp_error( $attach_id ) && $attach_id > 0 ) {
            $attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta( $order_id, '_bbpm_payment_receipt_id', $attach_id );
        }
        update_post_meta( $order_id, '_bbpm_payment_receipt_url', $uploaded_file['url'] );

        $order = wc_get_order( $order_id );
        $order->update_status( 'on-hold', __( 'Awaiting manual confirmation of bank transfer. Payment receipt uploaded.', 'bangladeshi-bank-payment-method' ) );
        $order->save();

        WC()->cart->empty_cart();

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

    public function thankyou_page( $order_id ) {
        // intentionally left blank
    }

public function display_payment_receipt_admin_order( $order ) {
    $receipt_url = get_post_meta( $order->get_id(), '_bbpm_payment_receipt_url', true );
    $receipt_id  = get_post_meta( $order->get_id(), '_bbpm_payment_receipt_id', true );

    if ( $receipt_url ) {
        echo '<div class="rsldv-admin-panel" style="all:initial; display:block; font-family:sans-serif; margin-top:20px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; overflow:hidden; width:100%; box-sizing:border-box;">';
            echo '<div style="padding:12px; background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:bold; color:#334155;">' . esc_html__( 'Payment Receipt Verification', 'bangladeshi-bank-payment-method' ) . '</div>';
            echo '<div style="padding:15px; text-align:center;">';
                if ( $receipt_id ) {
                    echo wp_get_attachment_image( $receipt_id, 'medium', false, array( 'style' => 'max-width:100%; height:auto; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.1);' ) );
                } else {
                    echo '<img src="' . esc_url( $receipt_url ) . '" style="max-width:100%; border-radius:4px;" />';
                }
                echo '<div style="margin-top:15px; display:flex; gap:10px; justify-content:center;">';
                    echo '<a href="' . esc_url( $receipt_url ) . '" target="_blank" class="button button-primary" style="all:revert; cursor:pointer;">' . esc_html__( 'View Full Image', 'bangladeshi-bank-payment-method' ) . '</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
}
}
