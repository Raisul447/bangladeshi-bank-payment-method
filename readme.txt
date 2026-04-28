=== Bangladeshi Bank Payment Method ===
Contributors: shagor447
Tags: woocommerce, payment gateway, bangladesh bank transfer, manual payment, bangladeshi bank payment gateway
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.1.7
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

WooCommerce gateway for Bangladeshi businesses allowing customers to upload bank payment receipts at checkout.

== Description ==
This plugin adds a secure **Bank Payment with Receipt Upload** option to your WooCommerce store, specially designed for merchants and customers in Bangladesh.
Instead of just entering a transaction ID, customers can **upload a screenshot or photo of their bank payment receipt** (e.g., mobile banking confirmation) directly on the checkout page. The uploaded image is securely stored and displayed in the order details for easy manual verification by the store admin.

**Perfect for businesses that require visual proof of payment before processing orders.**

**Features:**
* Accept bank transfer payments from any Bangladeshi bank (City Bank, IFIC BANK, UCB Bank, Islami Bank etc.).
* Customers upload a **payment receipt image** (PNG/JPG) during checkout.
* Automatic file validation (max 5MB, only images allowed).
* Uploaded receipt is visible in the **WooCommerce order details** in the admin dashboard.
* Displays your bank account details clearly on the checkout page.
* Fully compatible with WooCommerce emails, order statuses, and cart flow.
* You can change your bank icon, it will make it visually clear and easier for customers to understand.
* No sensitive data stored — secure and lightweight.

== Installation ==
1. Upload the `bangladeshi-bank-payment-method` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **WooCommerce → Settings → Payments**.
4. Find **"Bangladeshi Bank Payment Method"** and click **Manage**.
5. Enable the gateway, enter your bank details, and save changes.
6. Customers will now see this option at checkout and can upload their payment proof.

== Frequently Asked Questions ==

= Can customers use this with any Bank App? =
Yes! Customers can upload a screenshot of their Bank transaction confirmation as the payment receipt.

= What file types are allowed? =
Only **JPG, JPEG, and PNG** images are accepted. Maximum file size: **5MB**.

= Where can I see the uploaded receipt? =
Go to **WooCommerce → Orders → [Order]**. The receipt image appears under the billing address section.

= Does this plugin auto-verify payments? =
No. Payments are marked as **"On Hold"** until you manually verify the uploaded receipt.

= Is this plugin compatible with other payment methods? =
Yes. It works alongside PayPal, Stripe, Cash on Delivery, and other gateways.

== Screenshots ==
1. Checkout page overview (customer view).
2. View uploaded payment receipt in order details.
3. Bank account setup and management in payment settings.

== Changelog ==

= 1.1.7 =
* Fix: Fixed the "Payment Pending" and infinite loading issues to ensure smooth order placement.
* Fix: Removed global namespace function to comply with WordPress Naming Conventions (Non-Prefixed Function error).
* Fix: Optimized asset enqueuing using anonymous functions for better security and compatibility.

= 1.0.8 =
* Fixed: Resolved "Security check failed" error during checkout when a user tries to create a new account while using this payment gateway.
* Fixed: Resolved infinite loading issue on the "Order Received" page by optimizing the checkout AJAX flow and standardizing redirect logic.
* Improved: Enhanced the Admin Order Details UI with a refined receipt verification box and a "View Full Image" button for better accessibility.
* Compliance: Fully sanitized all $_POST and $_FILES inputs to adhere to WordPress Security Coding Standards (WPCS).

= 1.0.7 =
* Improve: Enhanced the checkout user interface with a modern, card-based layout for bank details.
* Fixed: Resolved "Choose File" border breaking issue on mobile devices.
* Fixed: Increased image upload size limit to 5MB to support high-quality receipts.
* Tested: Fully compatible with the latest WordPress and WooCommerce versions.

= 1.0.6 =
* Fixed: Image upload size increased to 1MB.
* Tested with the latest WordPress version.
* Fixed minor bugs.

= 1.0.5 =
* Fix: Missing data.

= 1.0.4 =
* Major fix: Resolved critical conflict where inline script usage broke file upload functionality.
* Compliance: Removed direct inline <script> tag to adhere to WordPress plugin submission guidelines.
* Fix: Ensured file size validation and AJAX disable logic now function correctly using standard JS enqueuing methods.

= 1.0.3 =
* Disabled WooCommerce AJAX checkout when this gateway is selected to ensure file uploads work reliably.
* Improved JavaScript isolation and form handling.

= 1.0.2 =
* Fixed "Please upload a payment receipt" error on checkout.
* Added proper translators comment and PHPCS compliance.
* Enhanced security and file validation.

= 1.0.1 =
* Minor CSS and UI improvements.
* Better error handling for file uploads.

= 1.0.0 =
* Initial release.

== Update Notice ==
= 1.1.7 =
Fixed the "Payment Pending" and infinite loading issues to ensure smooth order placement. Highly recommended for all users to ensure a stable and secure checkout experience.
