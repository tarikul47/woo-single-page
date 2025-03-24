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

			// Add filing option
			if (!empty($custom_data['filing_option'])) {
				$filing_options = array(
					'basic' => 'Basic ($99)',
					'gold' => 'Gold ($199)',
					'premium' => 'Premium ($299)',
				);

				$item_data[] = array(
					'key' => 'Filing Option',
					'value' => isset($filing_options[$custom_data['filing_option']]) ? $filing_options[$custom_data['filing_option']] : ucfirst($custom_data['filing_option']),
				);
			}

			// Add addon selection
			if (!empty($custom_data['addon_selection'])) {
				$addon_options = array(
					'business_presence' => 'Texas Business Presence Package ($50)',
					'corporate_supplies' => 'Corporate Supplies ($75)',
					's_corporation' => 'S Corporation ($100)',
					'ein' => 'Tax ID / EIN ($50)',
					'trade_name' => 'Trade Name (DBA) ($100)',
				);

				$item_data[] = array(
					'key' => 'Addon',
					'value' => isset($addon_options[$custom_data['addon_selection']]) ? $addon_options[$custom_data['addon_selection']] : ucfirst(str_replace('_', ' ', $custom_data['addon_selection'])),
				);
			}

			// Add price details
			if (isset($custom_data['base_price'])) {
				$item_data[] = array(
					'key' => 'Base Price',
					'value' => wc_price($custom_data['base_price']),
				);
			}

			if (isset($custom_data['addon_price'])) {
				$item_data[] = array(
					'key' => 'Addon Price',
					'value' => wc_price($custom_data['addon_price']),
				);
			}

			if (isset($custom_data['total_price'])) {
				$item_data[] = array(
					'key' => 'Total Custom Price',
					'value' => wc_price($custom_data['total_price']),
				);
			}
		}

		return $item_data;
	}

	/**
	 * Save custom data to order
	 */
	public function wsp_woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
	{
		if (isset($values['custom_data'])) {
			$custom_data = $values['custom_data'];

			// Save company names
			if (!empty($custom_data['company_names'])) {
				$item->add_meta_data('Companies', implode(', ', $custom_data['company_names']));
			}

			// Save business type
			if (!empty($custom_data['business_type'])) {
				$item->add_meta_data('Business Type', ucfirst($custom_data['business_type']));
			}

			// Save management type and related data
			if (!empty($custom_data['management_type'])) {
				$item->add_meta_data('Management Type', ucfirst($custom_data['management_type']));

				if ($custom_data['management_type'] === 'member' && !empty($custom_data['members'])) {
					for ($i = 0; $i < count($custom_data['members']['names']); $i++) {
						$member_data = array();

						if (!empty($custom_data['members']['names'][$i])) {
							$member_data[] = 'Name: ' . $custom_data['members']['names'][$i];
						}

						if (!empty($custom_data['members']['emails'][$i])) {
							$member_data[] = 'Email: ' . $custom_data['members']['emails'][$i];
						}

						if (!empty($custom_data['members']['phones'][$i])) {
							$member_data[] = 'Phone: ' . $custom_data['members']['phones'][$i];
						}

						if (!empty($member_data)) {
							$item->add_meta_data('Member ' . ($i + 1), implode(', ', $member_data));
						}
					}
				} elseif ($custom_data['management_type'] === 'manager' && !empty($custom_data['managers'])) {
					for ($i = 0; $i < count($custom_data['managers']['names']); $i++) {
						$manager_data = array();

						if (!empty($custom_data['managers']['names'][$i])) {
							$manager_data[] = 'Name: ' . $custom_data['managers']['names'][$i];
						}

						if (!empty($custom_data['managers']['emails'][$i])) {
							$manager_data[] = 'Email: ' . $custom_data['managers']['emails'][$i];
						}

						if (!empty($custom_data['managers']['phones'][$i])) {
							$manager_data[] = 'Phone: ' . $custom_data['managers']['phones'][$i];
						}

						if (!empty($manager_data)) {
							$item->add_meta_data('Manager ' . ($i + 1), implode(', ', $manager_data));
						}
					}
				}
			}

			// Save filing option
			if (!empty($custom_data['filing_option'])) {
				$filing_options = array(
					'basic' => 'Basic ($99)',
					'gold' => 'Gold ($199)',
					'premium' => 'Premium ($299)',
				);

				$item->add_meta_data('Filing Option', isset($filing_options[$custom_data['filing_option']]) ? $filing_options[$custom_data['filing_option']] : ucfirst($custom_data['filing_option']));
			}

			// Save addon selection
			if (!empty($custom_data['addon_selection'])) {
				$addon_options = array(
					'business_presence' => 'Texas Business Presence Package ($50)',
					'corporate_supplies' => 'Corporate Supplies ($75)',
					's_corporation' => 'S Corporation ($100)',
					'ein' => 'Tax ID / EIN ($50)',
					'trade_name' => 'Trade Name (DBA) ($100)',
				);

				$item->add_meta_data('Addon', isset($addon_options[$custom_data['addon_selection']]) ? $addon_options[$custom_data['addon_selection']] : ucfirst(str_replace('_', ' ', $custom_data['addon_selection'])));
			}

			// Save price details
			if (isset($custom_data['base_price'])) {
				$item->add_meta_data('Base Price', wc_price($custom_data['base_price']));
			}

			if (isset($custom_data['addon_price'])) {
				$item->add_meta_data('Addon Price', wc_price($custom_data['addon_price']));
			}

			if (isset($custom_data['total_price'])) {
				$item->add_meta_data('Total Custom Price', wc_price($custom_data['total_price']));
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
