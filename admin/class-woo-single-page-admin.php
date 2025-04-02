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

	private function add_feature_section($type, $label, $post)
	{
		echo '<div class="options_group feature-group">';
		echo '<h3>' . esc_html($label) . '</h3>';

		// Features container
		echo '<div class="features-list" data-type="' . $type . '_features">';
		$features = get_post_meta($post->ID, '_' . $type . '_features', true);

		if (!empty($features)) {
			foreach ($features as $index => $feature) {
				echo '<div class="feature-entry">';
				echo '<input type="text" name="' . $type . '_features[' . $index . '][title]" 
                   value="' . esc_attr($feature['title']) . '" 
                   placeholder="Feature Title">';
				echo '<button type="button" class="button remove-feature">' . __('Remove') . '</button>';
				echo '</div>';
			}
		}
		echo '</div>';

		// Add button
		echo '<button type="button" class="button add-feature" data-type="' . $type . '_features">'
			. __('Add Feature') . '</button>';
		echo '</div>';
	}

	private function add_filing_options_section($type, $label, $post)
	{
		echo '<div class="options_group filing-options-group">';
		echo '<h3>' . esc_html($label) . '</h3>';

		// Enable option
		// woocommerce_wp_checkbox(array(
		// 	'id' => '_enable_' . $type . '_filing',
		// 	'label' => __('Enable ' . $label, 'custom-product-checkout'),
		// 	'value' => get_post_meta($post->ID, '_enable_' . $type . '_filing', true)
		// ));

		// Addons container
		echo '<div class="' . $type . '-addons-container">';
		$addons = get_post_meta($post->ID, '_' . $type . '_filing_addons', true);
		echo '<div class="addons-list" data-type="' . $type . '_filing_addons">';
		if (!empty($addons)) {
			foreach ($addons as $index => $addon) {
				echo '<div class="addon-entry">';
				echo '<input type="text" name="' . $type . '_filing_addons[' . $index . '][label]" 
					   value="' . esc_attr($addon['label']) . '" placeholder="Addon Label">';
				echo '<input type="number" name="' . $type . '_filing_addons[' . $index . '][price]" 
					   value="' . esc_attr($addon['price']) . '" placeholder="Price" step="0.01">';
				echo '<button type="button" class="button remove-addon">' . __('Remove') . '</button>';
				echo '</div>';
			}
		}
		echo '</div></div>';

		// Button with matching data-type
		echo '<button type="button" class="button add-addon" data-type="' . $type . '_filing_addons">' . __('Add Addon') . '</button>';
		echo '</div>';
	}

	private function add_business_addons_section($post)
	{
		echo '<div class="options_group business-addons-group">';
		echo '<h3>' . __('Business Addons', 'custom-product-checkout') . '</h3>';

		$addons = get_post_meta($post->ID, '_business_addons', true);
		echo '<div class="addons-list" data-type="business_addons">';
		if (!empty($addons)) {
			foreach ($addons as $index => $addon) {
				echo '<div class="addon-entry">';
				echo '<input type="text" name="business_addons[' . $index . '][label]" 
					   value="' . esc_attr($addon['label']) . '" placeholder="Addon Label">';
				echo '<input type="number" name="business_addons[' . $index . '][price]" 
					   value="' . esc_attr($addon['price']) . '" placeholder="Price" step="0.01">';
				echo '<input type="hidden" name="business_addons[' . $index . '][type]" value="checkbox">';
				echo '<button type="button" class="button remove-addon">' . __('Remove') . '</button>';
				echo '</div>';
			}
		}
		echo '</div>';

		// Button with matching data-type
		echo '<button type="button" class="button add-addon" data-type="business_addons">' . __('Add Addon') . '</button>';
		echo '</div>';
	}

	// ---------------------------------------------

	/**
	 * Add multiple meta boxes
	 */
	public function add_custom_meta_boxes()
	{
		// Options Meta Box
		add_meta_box(
			'custom_checkout_options',
			__('Custom Checkout Options', 'custom-product-checkout'),
			array($this, 'render_options_meta_box'),
			'product',
			'normal',
			'high'
		);

		// Description Meta Box
		add_meta_box(
			'package_descriptions',
			__('Package Descriptions', 'custom-product-checkout'),
			array($this, 'render_descriptions_meta_box'),
			'product',
			'normal',
			'high'
		);

		// Add visibility classes to both meta boxes
		add_filter('postbox_classes_product_custom_checkout_options', array($this, 'add_meta_box_classes'));
		add_filter('postbox_classes_product_package_descriptions', array($this, 'add_meta_box_classes'));
	}

	/**
	 * Add visibility classes to meta boxes
	 */
	public function add_meta_box_classes($classes)
	{
		array_push($classes, 'show_if_simple', 'show_if_variable');
		return $classes;
	}

	/**
	 * Render Options Meta Box
	 */
	public function render_options_meta_box($post)
	{
		wp_nonce_field('custom_checkout_meta_save', 'custom_checkout_meta_nonce');

		echo '<div class="options-container">';

		// Enable Checkbox
		echo '<div class="options_group">';
		woocommerce_wp_checkbox(array(
			'id' => '_enable_custom_checkout',
			'label' => __('Enable Custom Checkout Options', 'custom-product-checkout'),
			'description' => __('Enable to show custom checkout options', 'custom-product-checkout')
		));
		echo '</div>';

		// Filing Options
		$this->add_filing_options_section('basic', 'Basic Options', $post);
		$this->add_filing_options_section('gold', 'Gold Options', $post);
		$this->add_filing_options_section('premium', 'Premium Options', $post);

		// Business Addons
		$this->add_business_addons_section($post);

		echo '</div>';
	}

	/**
	 * Render Descriptions Meta Box
	 */
	public function render_descriptions_meta_box($post)
	{
		echo '<div class="descriptions-container">';

		// Feature Sections
		$this->add_feature_section('basic', 'Basic Features', $post);
		$this->add_feature_section('gold', 'Gold Features', $post);
		$this->add_feature_section('premium', 'Premium Features', $post);

		echo '</div>';
	}

	// -----------------------------------------------



	public function save_product_data($post_id)
	{
		// Save enable option
		$enable = isset($_POST['_enable_custom_checkout']) ? 'yes' : 'no';
		update_post_meta($post_id, '_enable_custom_checkout', $enable);

		// Save feature sections
		$package_types = ['basic', 'gold', 'premium'];

		foreach ($package_types as $type) {
			if (!empty($_POST[$type . '_features'])) {
				$features = array_map(function ($feature) {
					return [
						'title' => sanitize_text_field($feature['title']),
						'value' => sanitize_title($feature['title'])
					];
				}, $_POST[$type . '_features']);
				update_post_meta($post_id, '_' . $type . '_features', $features);
			} else {
				delete_post_meta($post_id, '_' . $type . '_features');
			}
		}

		// Save filing options
		$types = ['basic', 'gold', 'premium'];

		foreach ($types as $type) {
			// Save enable status
			$enable_filing = isset($_POST['_enable_' . $type . '_filing']) ? 'yes' : 'no';
			update_post_meta($post_id, '_enable_' . $type . '_filing', $enable_filing);

			// Save addons
			if (!empty($_POST[$type . '_filing_addons'])) {
				$addons = array_values(array_map(function ($addon) {
					return [
						'label' => sanitize_text_field($addon['label']),
						'price' => (float) $addon['price'],
						'value' => sanitize_title($addon['label'])
					];
				}, $_POST[$type . '_filing_addons']));
				update_post_meta($post_id, '_' . $type . '_filing_addons', $addons);
			}
		}

		// Save business addons
		if (!empty($_POST['business_addons'])) {
			$business_addons = array_values(array_map(function ($addon) {
				return [
					'label' => sanitize_text_field($addon['label']),
					'price' => (float) $addon['price'],
					'type' => 'checkbox',
					'value' => sanitize_title($addon['label'])
				];
			}, $_POST['business_addons']));
			update_post_meta($post_id, '_business_addons', $business_addons);
		}
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
		$order_summary = isset($_POST['order_summary']) ? $_POST['order_summary'] : array();
		$total_price = isset($_POST['total_price']) ? $_POST['total_price'] : array();

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

		//	error_log('Adding product ID to cart: ' . $product_id);

		// Validate required fields
		$company_names = $this->get_form_values($cart_items, 'company_name');

		if (empty($company_names)) {
			wp_send_json_error(array('message' => 'Company name is required'));
			exit;
		}

		// Validate management information
		// $management_type = $this->get_form_value($cart_items, 'management_type');
		// if ($management_type === 'member') {
		// 	$member_names = $this->get_form_values($cart_items, 'member_name');
		// 	$member_emails = $this->get_form_values($cart_items, 'member_email');

		// 	if (empty($member_names) || empty($member_emails)) {
		// 		wp_send_json_error(array('message' => 'Member information is required'));
		// 		exit;
		// 	}
		// } else {
		// 	$manager_names = $this->get_form_values($cart_items, 'manager_name');
		// 	$manager_emails = $this->get_form_values($cart_items, 'manager_email');

		// 	if (empty($manager_names) || empty($manager_emails)) {
		// 		wp_send_json_error(array('message' => 'Manager information is required'));
		// 		exit;
		// 	}
		// }


		// Get members and managers (only names)
		$member_names = $this->get_form_values($cart_items, 'member_name');
		$manager_names = $this->get_form_values($cart_items, 'manager_name');

		//error_log('member_names items: ' . print_r($member_names, true));
		error_log('manager_names items: ' . print_r($cart_items, true));
		error_log('order_summary items: ' . print_r($order_summary, true));
		error_log('total_price items: ' . print_r($total_price, true));

		// Validate addon selection
		$addon_selection = $this->get_form_value($cart_items, 'addon_selection[]');
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
			//'management_type' => $management_type,
			'filing_option' => $filing_option,
			'addon_selection' => $addon_selection,
			'base_price' => $base_price,
			'addon_price' => $addon_price,
			'total_price' => $total_price,
			'order_summary' => $order_summary,
		);

		// Add members if available
		if (!empty($member_names)) {
			$custom_data['members'] = $member_names;
		}

		// Add managers if available
		if (!empty($manager_names)) {
			$custom_data['managers'] = $manager_names;
		}

		//error_log('manager_names items: ' . print_r($manager_names, true));
		error_log('custom_data raju items: ' . print_r($custom_data, true));

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
	// private function get_form_values($cart_items, $key)
	// {
	// 	$values = array();

	// 	foreach ($cart_items as $item) {
	// 		if ($item['name'] === $key . '[]') {
	// 			$values[] = sanitize_text_field($item['value']);
	// 		}
	// 	}

	// 	return $values;
	// }

	private function get_form_values($cart_items, $key)
	{
		$values = [];

		foreach ($cart_items as $item) {
			if (strpos($item['name'], $key) !== false) { // Handle array values properly
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
		global $pagenow;

		if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && isset($_GET['post'])) {
			$post_type = get_post_type($_GET['post']);
			if ($post_type === 'product') {
				wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-single-page-admin.css', array(), $this->version, 'all');
			}
		}

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

		global $pagenow;

		if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && isset($_GET['post'])) {
			$post_type = get_post_type($_GET['post']);
			if ($post_type === 'product') {
				wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-single-page-admin.js', array('jquery'), $this->version, false);
			}
		}

	}

}
