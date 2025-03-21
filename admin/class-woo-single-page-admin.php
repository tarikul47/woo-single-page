<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://onlytarikul.com
 * @since      1.0.0
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/admin
 * @author     Tarikul Islam <tarikul47@gmail.com>
 */
class Woo_Single_Page_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Process custom form submission and add to cart
	 */
	public function process_custom_add_to_cart()
	{
		error_log('Processing custom add to cart');

		// Verify nonce for security
		if (isset($_POST['security']) && !wp_verify_nonce($_POST['security'], 'custom_add_to_cart_nonce')) {
			error_log('Nonce verification failed');
			wp_send_json_error(array('message' => 'Security check failed'));
			exit;
		}

		// Get form data
		$cart_items = isset($_POST['cart_items']) ? $_POST['cart_items'] : array();
		error_log('Cart items: ' . print_r($cart_items, true));

		// Get product ID
		$product_id = 0;
		foreach ($cart_items as $item) {
			if ($item['name'] === 'product_id') {
				$product_id = absint($item['value']);
				break;
			}
		}

		if (!$product_id) {
			error_log('Product ID is missing');
			wp_send_json_error(array('message' => 'Product ID is missing'));
			exit;
		}

		// Check if product exists
		$product = wc_get_product($product_id);
		if (!$product) {
			error_log('Invalid product ID: ' . $product_id);
			wp_send_json_error(array('message' => 'Invalid product'));
			exit;
		}

		error_log('Adding product ID to cart: ' . $product_id);

		// Validate required fields
		$company_names = $this->get_form_values($cart_items, 'company_name');
		if (empty($company_names)) {
			wp_send_json_error(array('message' => 'Company name is required'));
			exit;
		}

		// Validate management information
		$management_type = $this->get_form_value($cart_items, 'management_type');
		if ($management_type === 'member') {
			$member_names = $this->get_form_values($cart_items, 'member_name');
			$member_emails = $this->get_form_values($cart_items, 'member_email');

			if (empty($member_names) || empty($member_emails)) {
				wp_send_json_error(array('message' => 'Member information is required'));
				exit;
			}
		} else {
			$manager_names = $this->get_form_values($cart_items, 'manager_name');
			$manager_emails = $this->get_form_values($cart_items, 'manager_email');

			if (empty($manager_names) || empty($manager_emails)) {
				wp_send_json_error(array('message' => 'Manager information is required'));
				exit;
			}
		}

		// Validate addon selection
		$addon_selection = $this->get_form_value($cart_items, 'addon_selection');
		if (empty($addon_selection)) {
			wp_send_json_error(array('message' => 'Business addon selection is required'));
			exit;
		}

		// Get filing option and calculate price
		$filing_option = $this->get_form_value($cart_items, 'filing_option');
		$base_price = 99.0; // Default base price

		if ($filing_option === 'gold') {
			$base_price = 199.0;
		} elseif ($filing_option === 'premium') {
			$base_price = 299.0;
		}

		// Add addon price
		$addon_price = 0;
		switch ($addon_selection) {
			case 'business_presence':
				$addon_price = 50.0;
				break;
			case 'corporate_supplies':
				$addon_price = 75.0;
				break;
			case 's_corporation':
				$addon_price = 100.0;
				break;
			case 'ein':
				$addon_price = 50.0;
				break;
			case 'trade_name':
				$addon_price = 100.0;
				break;
		}

		$total_price = $base_price + $addon_price;

		// Prepare custom data to be stored with the cart item
		$custom_data = array(
			'company_names' => $company_names,
			'business_type' => $this->get_form_value($cart_items, 'business_type'),
			'management_type' => $management_type,
			'filing_option' => $filing_option,
			'addon_selection' => $addon_selection,
			'base_price' => $base_price,
			'addon_price' => $addon_price,
			'total_price' => $total_price
		);

		// Add member or manager data if present
		if ($management_type === 'member') {
			$custom_data['members'] = array(
				'names' => $member_names,
				'emails' => $member_emails,
				'phones' => $this->get_form_values($cart_items, 'member_phone'),
			);
		} else {
			$custom_data['managers'] = array(
				'names' => $manager_names,
				'emails' => $manager_emails,
				'phones' => $this->get_form_values($cart_items, 'manager_phone'),
			);
		}

		// Make sure WC is loaded and cart is available
		if (!function_exists('WC') || !isset(WC()->cart)) {
			error_log('WooCommerce cart is not available');
			wp_send_json_error(array('message' => 'WooCommerce cart is not available'));
			exit;
		}

		// Empty the cart first to avoid multiple items (optional - remove if you want to allow multiple items)
		WC()->cart->empty_cart();

		// Add to cart
		try {
			$cart_item_key = WC()->cart->add_to_cart($product_id, 1, 0, array(), array('custom_data' => $custom_data));

			if ($cart_item_key) {
				error_log('Product added to cart successfully: ' . $cart_item_key);
				wp_send_json_success(array(
					'message' => 'Product added to cart successfully',
					'redirect' => wc_get_checkout_url(),
				));
			} else {
				error_log('Error adding product to cart');
				wp_send_json_error(array('message' => 'Error adding product to cart'));
			}
		} catch (Exception $e) {
			error_log('Exception when adding to cart: ' . $e->getMessage());
			wp_send_json_error(array('message' => 'Error: ' . $e->getMessage()));
		}

		exit;
	}

	/**
	 * Helper function to get form values from cart items array
	 */
	private function get_form_values($cart_items, $key)
	{
		$values = array();

		foreach ($cart_items as $item) {
			if ($item['name'] === $key . '[]') {
				$values[] = sanitize_text_field($item['value']);
			}
		}

		return $values;
	}

	/**
	 * Helper function to get a single form value from cart items array
	 */
	private function get_form_value($cart_items, $key)
	{
		foreach ($cart_items as $item) {
			if ($item['name'] === $key) {
				return sanitize_text_field($item['value']);
			}
		}

		return '';
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-single-page-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-single-page-admin.js', array('jquery'), $this->version, false);

	}

}
