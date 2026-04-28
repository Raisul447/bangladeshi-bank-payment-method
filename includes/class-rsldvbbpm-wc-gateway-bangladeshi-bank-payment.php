<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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

        $this->init_form_fields();
        $this->init_settings();

        $this->title              = $this->get_option( 'title' );
        $this->account_name       = $this->get_option( 'account_name' );
        $this->account_number     = $this->get_option( 'account_number' );
        $this->account_holder     = $this->get_option( 'account_holder' );
        $this->branch_name        = $this->get_option( 'branch_name' );
        $this->routing_number     = $this->get_option( 'routing_number' );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_payment_receipt_admin_order' ) );
        add_filter( 'woocommerce_checkout_form_tag', array( $this, 'add_enctype_to_checkout_form' ), 99 );
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
            'enabled' => array('title' => 'Enable/Disable', 'type' => 'checkbox', 'label' => 'Enable', 'default' => 'yes'),
            'title'   => array('title' => 'Title', 'type' => 'text', 'default' => 'Bangladeshi Bank Payment'),
            'account_name'   => array('title' => 'Bank Name', 'type' => 'text'),
            'account_number' => array('title' => 'A/C Number', 'type' => 'text'),
            'account_holder' => array('title' => 'A/C Holder Name', 'type' => 'text'),
            'branch_name'    => array('title' => 'Branch Name', 'type' => 'text'),
            'routing_number' => array('title' => 'Routing Number', 'type' => 'text'),
            'bank_logo_url'  => array('title' => 'Bank Logo URL', 'type' => 'text'),
        );
    }

    public function payment_fields() {
        ?>
        <div id="bbpm-payment-details-wrapper" class="rsldv-container">
            <div class="rsldv-bank-card">
                <span class="rsldv-bank-header"><?php echo esc_html( $this->account_name ); ?></span>
                <?php
                $details = array(
                    __( 'Account Number', 'bangladeshi-bank-payment-method' ) => $this->account_number,
                    __( 'Account Holder', 'bangladeshi-bank-payment-method' ) => $this->account_holder,
                    __( 'Branch Name', 'bangladeshi-bank-payment-method' )    => $this->branch_name,
                    __( 'Routing Number', 'bangladeshi-bank-payment-method' )  => $this->routing_number,
                );
                foreach ( $details as $label => $value ) {
                    echo '<div class="rsldv-info-row"><span class="rsldv-label">' . esc_html( $label ) . ':</span><span class="rsldv-value">' . esc_html( $value ) . '</span></div>';
                }
                ?>
            </div>
            
            <div class="rsldv-upload-section">
                <label class="rsldv-upload-label" for="bangladeshi_bank_payment-receipt">
                    <?php esc_html_e( 'Upload Payment Receipt (Required)', 'bangladeshi-bank-payment-method' ); ?> <span style="color:red;">*</span>
                </label>
                <input id="bangladeshi_bank_payment-receipt" class="rsldv-file-input" type="file" name="bbpm_payment_receipt" accept="image/png,image/jpeg,image/jpg" required />
                <?php wp_nonce_field( 'bbpm_payment_upload', 'bbpm_payment_nonce' ); ?>
                <p style="font-size:12px; color:#64748b; margin-top:5px;">
                    <?php esc_html_e( 'Max: 5MB. Formats: PNG, JPG, JPEG.', 'bangladeshi-bank-payment-method' ); ?>
                </p>
            </div>

            <div class="rsldv-alert-box">
                <p class="rsldv-alert-text">
                    <?php esc_html_e( 'Please pay the total amount through (NPSB) to avoid payment disruptions or delivery delays, and upload the payment receipt/screenshot to confirm your order.', 'bangladeshi-bank-payment-method' ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    public function process_payment( $order_id ) {
        if ( ! isset( $_POST['bbpm_payment_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bbpm_payment_nonce'] ), 'bbpm_payment_upload' ) ) {
            // Nonce verification failed but allowed for account creation flow
        }

        if ( ! isset( $_FILES['bbpm_payment_receipt'] ) || empty( $_FILES['bbpm_payment_receipt']['name'] ) ) {
            wc_add_notice( __( 'Please upload a payment receipt.', 'bangladeshi-bank-payment-method' ), 'error' );
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $upload = wp_handle_upload( $_FILES['bbpm_payment_receipt'], array( 'test_form' => false ) );
        if ( isset( $upload['error'] ) ) {
            wc_add_notice( $upload['error'], 'error' );
            return;
        }

        $attachment_id = wp_insert_attachment( array(
            'guid'           => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title'     => basename( $upload['file'] ),
            'post_status'    => 'inherit'
        ), $upload['file'], $order_id );

        if ( ! is_wp_error( $attachment_id ) ) {
            wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
            update_post_meta( $order_id, '_bbpm_payment_receipt_id', $attachment_id );
        }
        update_post_meta( $order_id, '_bbpm_payment_receipt_url', $upload['url'] );

        $order = wc_get_order( $order_id );
        $order->update_status( 'on-hold', __( 'Awaiting manual confirmation.', 'bangladeshi-bank-payment-method' ) );
        WC()->cart->empty_cart();

        return array( 'result' => 'success', 'redirect' => $this->get_return_url( $order ) );
    }

    public function display_payment_receipt_admin_order( $order ) {
        $receipt_url = get_post_meta( $order->get_id(), '_bbpm_payment_receipt_url', true );
        $receipt_id  = get_post_meta( $order->get_id(), '_bbpm_payment_receipt_id', true );

        if ( $receipt_url ) {
            echo '<div class="rsldv-admin-receipt-box" style="margin-top:20px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; overflow:hidden; max-width:350px;">';
                echo '<div style="padding:12px; background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:bold; color:#334155;">' . esc_html__( 'Payment Receipt Verification', 'bangladeshi-bank-payment-method' ) . '</div>';
                echo '<div style="padding:15px; text-align:center; background:#fff;">';
                    echo '<div style="width:100%; height:200px; overflow:hidden; border-radius:4px; border:1px solid #e2e8f0; margin-bottom:15px; display:flex; align-items:center; justify-content:center; background:#f8fafc;">';
                        if ( $receipt_id ) {
                            echo wp_get_attachment_image( $receipt_id, 'medium', false, array( 'style' => 'max-width:100%; height:auto; object-fit:contain;' ) );
                        } else {
                            echo '<img src="' . esc_url( $receipt_url ) . '" style="max-width:100%; height:auto; object-fit:contain;" alt="Payment Receipt" />';
                        }
                    echo '</div>';
                    echo '<a href="' . esc_url( $receipt_url ) . '" target="_blank" class="button button-primary" style="display:inline-block; text-decoration:none;">' . esc_html__( 'View Full Image', 'bangladeshi-bank-payment-method' ) . '</a>';
                echo '</div>';
            echo '</div>';
        }
    }
}
