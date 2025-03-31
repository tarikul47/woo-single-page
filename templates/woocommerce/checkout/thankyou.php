<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined('ABSPATH') || exit;

$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
?>

<div class="tp-order-area woocommerce-order">
    <?php
    if ($order):
        do_action('woocommerce_before_thankyou', $order->get_id());
        ?>

        <div class="tp-order-inner">
            <div class="tp-order-bg"></div>
            <div class="row gx-0 align-items-center">
                <?php if ($order->has_status('failed')): ?>

                    <div class="col-xl-12">
                        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
                            <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'acadia'); ?>
                        </p>

                        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
                                class="button pay"><?php esc_html_e('Pay', 'acadia'); ?></a>
                            <?php if (is_user_logged_in()): ?>
                                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                                    class="button pay"><?php esc_html_e('My account', 'acadia'); ?></a>
                            <?php endif; ?>
                        </p>
                    </div>

                <?php else: ?>
                    <div class="col-lg-6">
                        <div class="tp-order-details">
                            <div class="tp-order-details-top text-center mb-70">
                                <div class="tp-order-details-icon">
                                    <span>
                                        <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M46 26V51H6V26" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M51 13.5H1V26H51V13.5Z" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M26 51V13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                            <path
                                                d="M26 13.5H14.75C13.0924 13.5 11.5027 12.8415 10.3306 11.6694C9.15848 10.4973 8.5 8.9076 8.5 7.25C8.5 5.5924 9.15848 4.00269 10.3306 2.83058C11.5027 1.65848 13.0924 1 14.75 1C23.5 1 26 13.5 26 13.5Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                            <path
                                                d="M26 13.5H37.25C38.9076 13.5 40.4973 12.8415 41.6694 11.6694C42.8415 10.4973 43.5 8.9076 43.5 7.25C43.5 5.5924 42.8415 4.00269 41.6694 2.83058C40.4973 1.65848 38.9076 1 37.25 1C28.5 1 26 13.5 26 13.5Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="tp-order-details-content">
                                    <h3 class="tp-order-details-title">
                                        <?php echo esc_html__('Your Order Confirmed', 'acadia'); ?></h3>
                                    <p
                                        class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                                        <?php echo apply_filters('woocommerce_thankyou_order_received_text', wp_kses_post('We will send you a shipping confirmation email as soon <br> as your order ships ', 'acadia'), $order); ?>
                                    </p>
                                </div>
                            </div>

                            <div
                                class="woocommerce-order-overview woocommerce-thankyou-order-details order_details tp-order-details-item-wrapper">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class=" tp-order-details-item">
                                            <h4><?php echo esc_html__('Order Date:', 'acadia'); ?></h4>
                                            <p><?php echo wc_format_datetime($order->get_date_created()); ?></p>
                                        </div>
                                    </div>

                                    <?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()): ?>
                                        <div class="col-sm-7">
                                            <div class="woocommerce-order-overview__date date tp-order-details-item">
                                                <h4><?php echo esc_html__('Email:', 'acadia'); ?></h4>
                                                <p><?php echo esc_html($order->get_billing_email()); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($order->get_order_number())): ?>
                                        <div class="col-sm-5">
                                            <div class="woocommerce-order-overview__order order tp-order-details-item">
                                                <h4><?php echo esc_html__('Order Number:', 'acadia'); ?></h4>
                                                <p><?php echo esc_html__('#', 'acadia');
                                                echo esc_html($order->get_order_number()); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($order->get_payment_method_title())): ?>
                                        <div class="col-sm-7">
                                            <div class="woocommerce-order-overview__payment-method method tp-order-details-item">
                                                <h4><?php echo esc_html__('Payment Method:', 'acadia'); ?></h4>
                                                <p><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="tp-order-info-wrapper">
                            <?php  do_action('woocommerce_thankyou', $order->get_id()); 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>


            </div>
        </div>

        <div class="tp-woo-order-payment-msg mt-40 mb-40">
            <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
        </div>


    <?php else: ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
            <?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'acadia'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </p>

    <?php endif; ?>
</div>

<?php
if ($show_customer_details) {
    wc_get_template('order/order-details-customer.php', array('order' => $order));
}
?>