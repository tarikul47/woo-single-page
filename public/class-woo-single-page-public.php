<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://onlytarikul.com
 * @since      1.0.0
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/public
 * @author     Tarikul Islam <tarikul47@gmail.com>
 */
class Woo_Single_Page_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public $product_id;
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->product_id = 104;
	}

	// custom design show condition 
	public function wsp_woocommerce_before_single_product_summary()
	{
		if (is_product() && $this->product_id == get_the_ID()) {
			include WOO_SINGLE_PAGE_PATH . 'public/partials/woo-single-page-public-display.php';
		}
	}

	/**
	 * Add custom data to cart item
	 */
	public function add_custom_data_to_cart($cart_item_data, $product_id, $variation_id)
	{
		if (isset($cart_item_data['custom_data'])) {
			// If custom data is already set (from our AJAX handler), use it
			return $cart_item_data;
		}

		return $cart_item_data;
	}

	/**
	 * Display custom data in cart
	 */
	public function wsp_woocommerce_get_item_data($item_data, $cart_item)
	{
		if (isset($cart_item['custom_data'])) {
			$custom_data = $cart_item['custom_data'];

			// Add company names
			if (!empty($custom_data['company_names'])) {
				$item_data[] = array(
					'key' => 'Companies',
					'value' => implode(', ', $custom_data['company_names']),
				);
			}

			// Add business type
			if (!empty($custom_data['business_type'])) {
				$item_data[] = array(
					'key' => 'Business Type',
					'value' => ucfirst($custom_data['business_type']),
				);
			}

			// Format Members
			if (!empty($custom_data['members'])) {
				$members_list = '';
				foreach ($custom_data['members'] as $index => $member) {
					$members_list .= ($index + 1) . '. ' . esc_html($member) . ' <br>';
				}

				$item_data[] = array(
					'key' => 'Members',
					'value' => trim($members_list), // Convert new lines to <br>
				);
			}

			// Format Managers
			if (!empty($custom_data['managers'])) {
				$managers_list = '';
				foreach ($custom_data['managers'] as $index => $manager) {
					$managers_list .= ($index + 1) . '. ' . esc_html($manager) . ' <br>';
				}

				$item_data[] = array(
					'key' => 'Managers',
					'value' => trim($managers_list), // Convert new lines to <br>
				);
			}
		}

		return $item_data;
	}

	/**
	 * Fee added 
	 */

	public function wsp_woocommerce_cart_calculate_fees($cart)
	{
		foreach (WC()->cart->get_cart() as $cart_item) {
			if (isset($cart_item['custom_data']['order_summary']) && is_array($cart_item['custom_data']['order_summary'])) {
				foreach ($cart_item['custom_data']['order_summary'] as $addon) {
					if (!empty($addon['name']) && isset($addon['price']) && is_numeric($addon['price'])) {
						WC()->cart->add_fee(esc_html($addon['name']), floatval($addon['price']));
					}
				}
			}
		}

		// Subtract base product price ($1) to balance the total
		$cart->add_fee('Base Product Discount', -1, false);
	}

	/**
	 * Save custom data to order
	 */

	public function wsp_woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
	{
		if (isset($values['custom_data'])) {
			$custom_data = $values['custom_data'];

			if (!empty($custom_data['company_names'])) {
				$item->add_meta_data('Companies', implode(', ', $custom_data['company_names']), true);
			}

			if (!empty($custom_data['business_type'])) {
				$item->add_meta_data('Business Type', ucfirst($custom_data['business_type']), true);
			}

			if (!empty($custom_data['members'])) {
				$members_list = '';
				foreach ($custom_data['members'] as $index => $member) {
					$members_list .= ($index + 1) . '. ' . esc_html($member);
				}
				$item->add_meta_data('Members', trim($members_list), true);
			}

			if (!empty($custom_data['managers'])) {
				$managers_list = '';
				foreach ($custom_data['managers'] as $index => $manager) {
					$managers_list .= ($index + 1) . '. ' . esc_html($manager);
				}
				$item->add_meta_data('Managers', trim($managers_list), true);
			}
		}
	}

	/**
	 * Add custom data to order emails
	 */
	public function wsp_woocommerce_email_order_details($order, $sent_to_admin, $plain_text, $email)
	{
		// Get order items
		$items = $order->get_items();

		// Check if there are any items
		if (!empty($items)) {
			echo '<h2>Order Details</h2>';
			echo '<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #e5e5e5;">';
			echo '<thead><tr><th class="td" scope="col">Item</th><th class="td" scope="col">Details</th></tr></thead><tbody>';

			foreach ($items as $item_id => $item) {
				// Get item meta data
				$meta_data = $item->get_meta_data();

				if (!empty($meta_data)) {
					echo '<tr><td class="td" style="vertical-align: top; border: 1px solid #e5e5e5;">' . $item->get_name() . '</td>';
					echo '<td class="td" style="vertical-align: top; border: 1px solid #e5e5e5;">';

					foreach ($meta_data as $meta) {
						if (in_array($meta->key, array('_product_id', '_variation_id', '_qty', '_tax_class', '_line_subtotal', '_line_subtotal_tax', '_line_total', '_line_tax', '_line_tax_data'))) {
							continue; // Skip WooCommerce internal meta
						}

						echo '<strong>' . wp_kses_post($meta->key) . ':</strong> ' . wp_kses_post($meta->value) . '<br />';
					}

					echo '</td></tr>';
				}
			}

			echo '</tbody></table>';
		}
	}

	public function wsp_add_specific_product_body_class($classes)
	{
		if (is_checkout() && !WC()->cart->is_empty()) {
			$specific_product_id = 104; // Replace with your product ID

			foreach (WC()->cart->get_cart() as $cart_item) {
				if ($cart_item['product_id'] == $specific_product_id) {
					$classes[] = 'specific-product-in-cart';
					break;
				}
			}
		}

		// For thank you page: Check order items
		if (is_order_received_page()) {
			$order_id = absint(get_query_var('order-received'));
			$order = wc_get_order($order_id);
			$specific_product_id = 104;

			if ($order) {
				foreach ($order->get_items() as $item) {
					if ($item->get_product_id() == $specific_product_id) {
						$classes[] = 'specific-product-in-cart';
						break;
					}
				}
			}
		}


		return $classes;
	}

	public function wsp_custom_enqueue_scripts()
	{
		if (is_product() && $this->product_id == get_the_ID()) {
			?>
			<style>
				.tp-woo-single-gallery-wrapper {
					display: none;
				}

				.tp-product-details-wrapper {
					display: none;
				}
			</style>
			<script>
				jQuery(document).ready(function ($) {
					$('.tp-product-details-top').each(function () {
						var $child = $(this).find('.col-lg-6').first(); // Select first .col-lg-6 in second depth
						if ($child.length > 0) {
							$child.removeClass('col-lg-6').addClass('col-lg-12');
						}
					});
				});
			</script>
			<?php
		}

		?>

		<style>
			/* body.specific-product-in-cart .woocommerce-checkout-review-order-table th.product-total,
																							body.specific-product-in-cart .woocommerce-checkout-review-order-table td.product-total,
																							body.specific-product-in-cart .woocommerce-checkout-review-order-table tr.cart-subtotal,
																							body.specific-product-in-cart .woocommerce-order-details .tp-order-info-list-header h4,
																							{
																							display: none !important;
																							}

																							body.specific-product-in-cart table.woocommerce-checkout-review-order-table tbody td,
																							body.specific-product-in-cart table.woocommerce-checkout-review-order-table tfoot tr th {
																								text-align: left !important;
																							}

																							body.specific-product-in-cart .tp-order-info-list ul li.tp-order-info-list-header:last-child {
																								display: none !important;
																							} */
		</style>

		<?php

		if (is_checkout()) {
			?>
			<!-- <script>
				(function ($) {
					// Function to hide the fee
					function hideFee() {
						if ($('body').hasClass('specific-product-in-cart')) {
							// Target the order-total row
							var $orderTotal = $('tr.order-total');
							// Get the immediate previous row (the fee)
							var $previousRow = $orderTotal.prev('tr.fee');
							if ($previousRow.length) {
								$previousRow.hide();
							}
						}
					}

					// Run on initial page load
					hideFee();

					// Re-run when checkout updates (AJAX)
					$(document).on('updated_checkout', function () {
						hideFee();
					});

					// Optional: Add a slight delay for edge cases
					setTimeout(hideFee, 100);
				})(jQuery);
			</script> -->
			<?php
		}

	}

	public function wsp_woocommerce_locate_template($template, $template_name, $template_path)
	{
		// Target ONLY the review-order.php template
		if ('checkout/review-order.php' === $template_name) {
			// Path to your plugin's template file
			$plugin_template_path = WOO_SINGLE_PAGE_PATH . '/templates/woocommerce/checkout/review-order.php';

			// Check if the file exists, then override
			if (file_exists($plugin_template_path)) {
				//	var_dump($plugin_template_path);

				$template = $plugin_template_path;
			}
		}// Target ONLY the review-order.php template
		if ('order/order-details.php' === $template_name) {
			// 	// Path to your plugin's template file
			$plugin_template_path = WOO_SINGLE_PAGE_PATH . '/templates/woocommerce/order/order-details.php';

			// Check if the file exists, then override
			if (file_exists($plugin_template_path)) {
				//	var_dump($plugin_template_path);

				$template = $plugin_template_path;
			}
		}

		if ('order/order-details-item.php' === $template_name) {
			// Path to your plugin's template file
			$plugin_template_path = WOO_SINGLE_PAGE_PATH . '/templates/woocommerce/order/order-details-item.php';

			// Check if the file exists, then override
			if (file_exists($plugin_template_path)) {
				//	var_dump($plugin_template_path);

				$template = $plugin_template_path;
			}
		}
		return $template;
	}

	public function wsp_woocommerce_checkout_create_order_fee_item($item, $fee_key, $fee, $order)
	{
		if (isset($fee->id)) {
			$item->add_meta_data('_hidden_fee_id', $fee->id, true);
		}
	}

	public function wsp_woocommerce_order_get_items($items, $order)
	{
		foreach ($items as $key => $item) {
			if (
				$item->get_type() === 'fee' &&
				$item->get_meta('_hidden_fee_id') === 'base-product-discount'
			) {
				unset($items[$key]);
			}
		}
		return $items;
	}

	public function wsp_admin_footer()
	{

		$screen = get_current_screen();


		//error_log(print_r('$screen', true));
		//error_log(print_r($screen, true));

		// Ensure we are on a single order edit page
		if (!$screen || $screen->id !== 'woocommerce_page_wc-orders') {
			error_log('❌ Not on order edit page');
			return;
		}

		// Fetch Order ID correctly
		$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Works in edit mode
		if (!$order_id) {
			error_log('❌ Order ID not found');
			return;
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			error_log('❌ Order object not found');
			return;
		}

		$target_product_id = 104; // Change this to your product ID
		$product_found = false;

		// Debug: Print all order items
		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
			error_log("🔎 Found Product ID: $product_id");
			if ($product_id == $target_product_id) {
				$product_found = true;
				error_log("✅ Target product ($target_product_id) found in order!");
				break;
			}
		}

		// Final Debug Output
		if (!$product_found) {
			error_log("❌ Target product ($target_product_id) NOT found in order.");
		}

		// If product is found, inject JavaScript
		if ($product_found) {
			?>
			<script>
				jQuery(document).ready(function ($) {
					alert('Product 104 found! Hiding elements.');

					// Remove table rows data
					$('.woocommerce_order_items .item_cost').hide();
					$('.woocommerce_order_items .quantity').hide();
					$('.woocommerce_order_items tbody:first .line_cost').hide();

					// Remove the first row in the order totals table (Items Subtotal)
					$('.wc-order-totals tbody tr:first').hide();
				});

			</script>
			<?php
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Single_Page_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Single_Page_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (is_product()) {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-single-page-public.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		global $post;
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Single_Page_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Single_Page_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (is_product()) {
			wp_enqueue_script('jquery');
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-single-page-public.js', array('jquery'), $this->version, false);

			// Add WooCommerce AJAX parameters
			wp_localize_script($this->plugin_name, 'wc_add_to_cart_params', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
				'cart_url' => wc_get_cart_url(),
				'checkout_url' => wc_get_checkout_url(),
				'product_id' => $post->ID,
				'product_price' => wc_get_product($post->ID)->get_price()
			));
		}
	}

}
