<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://onlytarikul.com
 * @since      1.0.0
 *
 * @package    Woo_Single_Page
 * @subpackage Woo_Single_Page/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

defined('ABSPATH') || exit;

global $product;

// Get product data
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
?>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>ORDER REGISTERED AGENT SERVICE</h1>
    </div>

    <div class="checkout-content">
        <div class="checkout-form-container">
            <form id="accordion-checkout-form" method="post">
                <?php wp_nonce_field('custom_add_to_cart_nonce', 'custom_form_nonce'); ?>
                <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>">

                <!-- Section 1: Company Name -->
                <div class="checkout-section" id="section-1">
                    <div class="section-header active" data-section="1">
                        <span class="section-number">01</span>
                        <h2>Company Name</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content active">
                        <div class="company-container">
                            <div class="company-entry">
                                <div class="form-group">
                                    <label for="company-name-1">Company Name <span class="required">*</span></label>
                                    <input type="text" id="company-name-1" name="company_name[]" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-company-btn">+ Add Another Company</button>
                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="2">Continue</button>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Business Type -->
                <div class="checkout-section" id="section-2">
                    <div class="section-header" data-section="2">
                        <span class="section-number">02</span>
                        <h2>Business Type</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div class="form-group">
                            <label for="business-type">Select Business Type <span class="required">*</span></label>
                            <select id="business-type" name="business_type" required>
                                <option value="llc">LLC</option>
                                <option value="corporation">Corporation</option>
                            </select>
                        </div>
                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="3">Continue</button>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Management Type -->
                <div class="checkout-section" id="section-3">
                    <div class="section-header" data-section="3">
                        <span class="section-number">03</span>
                        <h2>Management Type</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div id="member-container" class="management-container"></div>
                        <button type="button" class="add-member-btn">+ Add Another Member</button>

                        <div id="manager-container" class="management-container"></div>
                        <button type="button" class="add-manager-btn">+ Add Another Manager</button>

                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="4">Continue</button>
                        </div>
                    </div>
                    <!-- <div class="section-content">
                        <div class="form-group">
                            <label for="management-type">Select Management Type <span class="required">*</span></label>
                            <select id="management-type" name="management_type" required>
                                <option value="member">Member</option>
                                <option value="manager">Manager</option>
                            </select>
                        </div>

                        <div id="member-container" class="management-container">
                            <div class="member-entry">
                                <h3>Member Information</h3>
                                <div class="form-group">
                                    <label for="member-name-1">Name <span class="required">*</span></label>
                                    <input type="text" id="member-name-1" name="member_name[]" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-member-btn">+ Add Another Member</button>

                        <div id="manager-container" class="management-container" style="display: none;">
                            <div class="manager-entry">
                                <h3>Manager Information</h3>
                                <div class="form-group">
                                    <label for="manager-name-1">Name <span class="required">*</span></label>
                                    <input type="text" id="manager-name-1" name="manager_name[]" required="false">
                                </div>                               
                            </div>
                        </div>
                        <button type="button" class="add-manager-btn" style="display: none;">+ Add Another
                            Manager</button>

                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="4">Continue</button>
                        </div>
                    </div> -->
                </div>

                <!-- Section 4: Filing Options -->
                <div class="checkout-section" id="section-4">
                    <div class="section-header" data-section="4">
                        <span class="section-number">04</span>
                        <h2>Filing Options</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div class="filing-options">
                            <div class="filing-option">
                                <input type="radio" id="filing-basic" name="filing_option" value="basic" checked
                                    required>
                                <label for="filing-basic">
                                    <h3>Basic</h3>
                                    <p>Standard filing service</p>
                                    <span class="price">$99</span>
                                </label>
                            </div>
                            <div class="filing-option">
                                <input type="radio" id="filing-gold" name="filing_option" value="gold" required>
                                <label for="filing-gold">
                                    <h3>Gold</h3>
                                    <p>Premium filing service with additional benefits</p>
                                    <span class="price">$199</span>
                                </label>
                            </div>
                            <div class="filing-option">
                                <input type="radio" id="filing-premium" name="filing_option" value="premium" required>
                                <label for="filing-premium">
                                    <h3>Amazon Premium</h3>
                                    <p>Complete filing service with all features</p>
                                    <span class="price">$299</span>
                                </label>
                            </div>
                        </div>
                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="5">Continue</button>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Business Addons -->
                <div class="checkout-section" id="section-5">
                    <div class="section-header" data-section="5">
                        <span class="section-number">05</span>
                        <h2>Business Addons</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div class="business-addons">
                            <p><strong>Please select at least one addon <span class="required">*</span></strong></p>
                            <div class="addon-option">
                                <input type="radio" id="addon-presence" name="addon_selection" value="business_presence"
                                    data-price="50.00" required>
                                <label for="addon-presence">
                                    <h3>Texas Business Presence Package</h3>
                                    <p>Website and phone number for your business</p>
                                    <span class="price">$50.00</span>
                                </label>
                            </div>
                            <div class="addon-option">
                                <input type="radio" id="addon-corporate" name="addon_selection"
                                    value="corporate_supplies" data-price="75.00" required>
                                <label for="addon-corporate">
                                    <h3>Corporate Supplies</h3>
                                    <p>Corporate seal, stock certificates, and more</p>
                                    <span class="price">$75.00</span>
                                </label>
                            </div>
                            <div class="addon-option">
                                <input type="radio" id="addon-s-corp" name="addon_selection" value="s_corporation"
                                    data-price="100.00" required>
                                <label for="addon-s-corp">
                                    <h3>S Corporation</h3>
                                    <p>S Corporation filing with the IRS</p>
                                    <span class="price">$100.00</span>
                                </label>
                            </div>
                            <div class="addon-option">
                                <input type="radio" id="addon-ein" name="addon_selection" value="ein" data-price="50.00"
                                    required>
                                <label for="addon-ein">
                                    <h3>Tax ID / EIN</h3>
                                    <p>Federal Tax ID Number</p>
                                    <span class="price">$50.00</span>
                                </label>
                            </div>
                            <div class="addon-option">
                                <input type="radio" id="addon-tradename" name="addon_selection" value="trade_name"
                                    data-price="100.00" required>
                                <label for="addon-tradename">
                                    <h3>Trade Name (DBA)</h3>
                                    <p>Register a "Doing Business As" name</p>
                                    <span class="price">$100.00</span>
                                </label>
                            </div>
                        </div>
                        <div class="section-navigation">
                            <button type="submit" class="submit-order" id="submit-button">Submit Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <div class="summary-item">
                    <span class="item-name"><?php echo esc_html($product_name); ?></span>
                    <span class="item-price"><?php echo wc_price($product_price); ?></span>
                </div>
                <div class="summary-item filing-option-item" id="filing-option-summary" style="display: none;">
                    <span class="item-name">Filing Option: <span id="filing-option-name"></span></span>
                    <span class="item-price" id="filing-option-price"></span>
                </div>
                <!-- Addon items will be added dynamically -->
            </div>
            <div class="summary-total">
                <span class="total-label">Total</span>
                <span class="total-price" id="order-total"><?php echo wc_price($product_price); ?></span>
            </div>
            <div class="order-notes">
                <p>By clicking "Submit Order", you acknowledge your order details are correct and accept the <a
                        href="#">terms of service</a>.</p>
            </div>
            <div class="summary-submit">
                <button type="submit" class="submit-order" form="accordion-checkout-form">Submit Order</button>
            </div>
        </div>
    </div>
</div>