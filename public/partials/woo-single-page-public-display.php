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

function display_package_features($package_type)
{
    $features = get_post_meta(get_the_ID(), '_' . $package_type . '_features', true);

    if (empty($features) || !is_array($features)) {
        return;
    }

    echo '<ul>';
    foreach ($features as $feature) {
        if (isset($feature['title'])) {
            echo '<li><i class="fas fa-check"></i> ' . esc_html($feature['title']) . '</li>';
        }
    }
    echo '</ul>';
}


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
            <form id="accordion-checkout-form" method="post" enctype="multipart/form-data">
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
                        <div id="manager-container" class="management-container">
                            <div class="manager-entry">
                                <h3>Manager Information</h3>
                                <div class="form-group">
                                    <label for="manager-name-1">Name <span class="required">*</span></label>
                                    <input type="text" id="manager-name-1" name="manager_name[]" required="">
                                </div>
                                <button type="button" class="remove-manager-btn">Remove</button>
                            </div>
                        </div>
                        <div id="member-container" class="management-container"></div>
                        <div class="button-group">
                            <button type="button" class="add-member-btn">+ Add Another Member</button>
                            <button type="button" class="add-manager-btn">+ Add Another Manager</button>
                        </div>
                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="4">Continue</button>
                        </div>
                    </div>
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
                                </label>
                            </div>
                            <div class="filing-option">
                                <input type="radio" id="filing-gold" name="filing_option" value="gold" required>
                                <label for="filing-gold">
                                    <h3>Gold</h3>
                                </label>
                            </div>
                            <div class="filing-option">
                                <input type="radio" id="filing-premium" name="filing_option" value="premium" required>
                                <label for="filing-premium">
                                    <h3>Amazon Premium</h3>
                                </label>
                            </div>
                        </div>

                        <!-- Basic Package Addons -->
                        <div class="addon-container" id="basic-addons">
                            <h4>Choose an Addon for Basic Package</h4>

                            <?php
                            $basic_addons = get_post_meta($product_id, '_basic_filing_addons', true);

                            // echo "<pre>";
                            // print_r($basic_addons);
                            ?>
                            <div class="addons">
                                <?php
                                if ($basic_addons) {
                                    foreach ($basic_addons as $key => $data) {
                                        // Create a unique ID using the package name and key or value
                                        $input_id = 'basic-addon-' . esc_attr($key);
                                        ?>
                                        <div class="addon-option">
                                            <input type="radio" id="<?php echo $input_id; ?>" name="basic_addon"
                                                value="<?php echo esc_attr($data['value']); ?>"
                                                data-price="<?php echo esc_attr($data['price']); ?>">

                                            <label for="<?php echo $input_id; ?>">
                                                <h3><?php echo esc_html($data['label']); ?></h3>
                                                <span class="price">$<?php echo esc_html($data['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php }
                                } else {
                                    echo 'There is no addon';
                                }
                                ?>
                            </div>


                            <div class="package-details">
                                <h4>Basic Package Includes:</h4>
                                <?php display_package_features('basic'); ?>
                            </div>

                        </div>

                        <!-- Gold Package Addons -->
                        <div class="addon-container" id="gold-addons" style="display: none;">
                            <h4>Choose an Addon for Gold Package</h4>
                            <?php
                            $gold_addons = get_post_meta($product_id, '_gold_filing_addons', true);
                            ?>
                            <div class="addons">
                                <?php
                                if ($gold_addons) {
                                    foreach ($gold_addons as $key => $data) {
                                        // Create a unique ID using the package name and key or value
                                        $input_id = 'gold-addon-' . esc_attr($key);
                                        ?>
                                        <div class="addon-option">
                                            <input type="radio" id="<?php echo $input_id; ?>" name="gold_addon"
                                                value="<?php echo esc_attr($data['value']); ?>"
                                                data-price="<?php echo esc_attr($data['price']); ?>">

                                            <label for="<?php echo $input_id; ?>">
                                                <h3><?php echo esc_html($data['label']); ?></h3>
                                                <span class="price">$<?php echo esc_html($data['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php }
                                } else {
                                    echo 'There is no addon';
                                }
                                ?>
                            </div>


                            <!-- Gold Package Details Section with Ginger Icon -->
                            <div class="package-details">
                                <h4>Gold Package Includes:</h4>
                                <?php display_package_features('gold'); ?>
                            </div>
                        </div>

                        <!-- Premium Package Addons -->
                        <div class="addon-container" id="premium-addons" style="display: none;">
                            <h4>Choose an Addon for Premium Package</h4>
                            <?php
                            $premium_addons = get_post_meta($product_id, '_premium_filing_addons', true);
                            ?>
                            <div class="addons">
                                <?php
                                if ($premium_addons) {
                                    foreach ($premium_addons as $key => $data) {
                                        // Create a unique ID using the package name and key or value
                                        $input_id = 'premium-addon-' . esc_attr($key);
                                        ?>
                                        <div class="addon-option">
                                            <input type="radio" id="<?php echo $input_id; ?>" name="premium_addon"
                                                value="<?php echo esc_attr($data['value']); ?>"
                                                data-price="<?php echo esc_attr($data['price']); ?>">

                                            <label for="<?php echo $input_id; ?>">
                                                <h3><?php echo esc_html($data['label']); ?></h3>
                                                <span class="price">$<?php echo esc_html($data['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php }
                                } else {
                                    echo 'There is no addon';
                                }
                                ?>
                            </div>


                            <div class="package-details">
                                <h4>Premium Package Includes:</h4>
                                <?php display_package_features('premium'); ?>
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
                            <?php
                            $business_addons = get_post_meta($product_id, '_business_addons', true);
                            ?>

                            <div class="addons">
                                <?php
                                if ($business_addons) {
                                    foreach ($business_addons as $key => $data) { ?>
                                        <div class="addon-option">
                                            <input type="checkbox" id="<?php echo esc_attr_e($data['value']); ?>"
                                                name="addon_selection[]" value="<?php echo esc_attr_e($data['value']); ?>"
                                                data-price="<?php echo esc_attr_e($data['price']); ?>">
                                            <label for="<?php echo esc_attr_e($data['value']); ?>">
                                                <h3><?php echo esc_attr_e($data['label']); ?></h3>
                                                <span class="price">$<?php echo esc_attr_e($data['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php }
                                } else {
                                    ?>
                                    There is no addon
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="section-navigation">
                            <button type="button" class="continue-btn" data-next="6">Continue</button>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Passport and Driving license -->
                <div class="checkout-section" id="section-6">
                    <div class="section-header" data-section="6">
                        <span class="section-number">06</span>
                        <h2>Document Upload</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div class="business-addons">
                            <p>
                                <strong>Please select at least one document <span class="required">*</span>
                                </strong>
                            </p>
                            <div class="document-upload">
                                <div class="upload-instructions">
                                    <p class="requirements">
                                        <strong>Requirements:</strong><br>
                                        - PDF, JPG, or PNG format<br>
                                        - Maximum file size: 5MB<br>
                                        - Statement must be recent (within 3 months)
                                    </p>
                                </div>
                                <label><strong>Select Document <span class="required">*</span></strong></label>
                                <!-- Add a hidden input for document data -->
                                <input type="hidden" name="document_data" id="document_data">

                                <select name="document_option" class="document-select">
                                    <option value="">-- Please select --</option>
                                    <option value="passport">Passport</option>
                                    <option value="driving">Driving License</option>
                                </select>

                                <div id="passport-upload" class="document-input"
                                    style="display: none; margin-top: 10px;">
                                    <label for="passport_file">Upload Passport</label>
                                    <input type="file" id="passport_file" name="passport_file"
                                        accept="image/*,application/pdf">
                                </div>

                                <div id="driving-upload" class="document-input"
                                    style="display: none; margin-top: 10px;">
                                    <label for="driving_file">Upload Driving License</label>
                                    <input type="file" id="driving_file" name="driving_file"
                                        accept="image/*,application/pdf">
                                </div>
                            </div>

                            <div class="section-navigation">
                                <button type="button" class="continue-btn" data-next="7">Continue</button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Section 7: Bank Statement Upload -->
                <div class="checkout-section" id="section-7">
                    <div class="section-header" data-section="7">
                        <span class="section-number">07</span>
                        <h2>Bank Statement Upload</h2>
                        <span class="toggle-icon"></span>
                    </div>
                    <div class="section-content">
                        <div class="bank-statement-upload">
                            <div class="document-upload">
                                <div class="upload-instructions">
                                    <p class="requirements">
                                        <strong>Requirements:</strong><br>
                                        - PDF, JPG, or PNG format<br>
                                        - Maximum file size: 5MB<br>
                                        - Statement must be recent (within 3 months)
                                    </p>
                                </div>

                                <div class="bank-statement-input">
                                    <label for="bank_statement_file">
                                        Upload Bank Statement
                                        <span class="required">*</span>
                                    </label>
                                    <input type="file" id="bank_statement_file" name="bank_statement_file"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="file-requirements">
                                        Accepted formats: .pdf, .jpg, .jpeg, .png
                                    </div>
                                </div>
                            </div>

                            <div class="section-navigation">
                                <button type="button" class="continue-btn" data-next="8">Continue</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
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