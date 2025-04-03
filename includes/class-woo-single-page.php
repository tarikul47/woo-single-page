<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://onlytarikul.com
 * @since      1.0.0
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/includes
 * @author     Tarikul Islam <tarikul47@gmail.com>
 */
class Woo_Single_Page
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Single_Page_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WOO_SINGLE_PAGE_VERSION')) {
			$this->version = WOO_SINGLE_PAGE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woo-single-page';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Single_Page_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Single_Page_i18n. Defines internationalization functionality.
	 * - Woo_Single_Page_Admin. Defines all hooks for the admin area.
	 * - Woo_Single_Page_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-single-page-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woo-single-page-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woo-single-page-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-woo-single-page-public.php';

		$this->loader = new Woo_Single_Page_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Single_Page_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Woo_Single_Page_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Woo_Single_Page_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// In your main plugin class constructor:
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_custom_meta_boxes');

		// Save product data
		$this->loader->add_action('woocommerce_process_product_meta', $plugin_admin, 'save_product_data');

		// Register AJAX handler for form submission
		$this->loader->add_action('wp_ajax_custom_add_to_cart', $plugin_admin, 'process_custom_add_to_cart');
		$this->loader->add_action('wp_ajax_nopriv_custom_add_to_cart', $plugin_admin, 'process_custom_add_to_cart');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Woo_Single_Page_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		// woo single product custom design 
		$this->loader->add_action('woocommerce_before_single_product_summary', $plugin_public, 'wsp_woocommerce_before_single_product_summary', 20);

		// Add custom data to cart item
		//	$this->loader->add_action('woocommerce_add_cart_item_data', $plugin_public, 'wsp_woocommerce_add_cart_item_data');

		// Display custom data in cart and checkout
		$this->loader->add_action('woocommerce_get_item_data', $plugin_public, 'wsp_woocommerce_get_item_data', 10, 2);

		// Save custom data to order
		$this->loader->add_action('woocommerce_checkout_create_order_line_item', $plugin_public, 'wsp_woocommerce_checkout_create_order_line_item', 10, 4);

		// Display custom data in order emails
		//	$this->loader->add_action('woocommerce_email_order_details', $plugin_public, 'wsp_woocommerce_email_order_details', 10, 4);

		$this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_public, 'wsp_woocommerce_cart_calculate_fees');

		$this->loader->add_filter('body_class', $plugin_public, 'wsp_add_specific_product_body_class');

		$this->loader->add_filter('woocommerce_locate_template', $plugin_public, 'wsp_woocommerce_locate_template', 20, 3);

		// custom css product id for single product page to show custom design 
		$this->loader->add_action('wp_footer', $plugin_public, 'wsp_custom_enqueue_scripts');

		// Checkout page discount type product remove
		$this->loader->add_action('woocommerce_checkout_create_order_fee_item', $plugin_public, 'wsp_woocommerce_checkout_create_order_fee_item', 10, 4);
		$this->loader->add_filter('woocommerce_order_get_items', $plugin_public, 'wsp_woocommerce_order_get_items', 10, 2);

		//admin order age remove 
		$this->loader->add_action('admin_footer', $plugin_public, 'wsp_admin_footer');

		// Guest Register phone number required set 
		// 1. Make phone field required for guests
		$this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'wsp_custom_validate_guest_phone');
		$this->loader->add_filter('woocommerce_billing_fields', $plugin_public, 'wsp_custom_require_phone_for_guests', 20, 1);
		$this->loader->add_filter('woocommerce_form_field_phone', $plugin_public, 'wsp_custom_phone_field_validation', 10, 4);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Single_Page_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

}
