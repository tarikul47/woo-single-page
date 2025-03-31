<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.5.0
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if (!$order) {
    return;
}

$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', array('completed', 'processing')));
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads = $order->get_downloadable_items();
$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();

if ($show_downloads) {
    wc_get_template(
        'order/order-downloads.php',
        array(
            'downloads' => $downloads,
            'show_title' => true,
        )
    );
}
?>

<div class="woocommerce-order-details">
    <?php do_action('woocommerce_order_details_before_order_table', $order); ?>

    <h4 class="woocommerce-order-details__title tp-order-info-title"><?php esc_html_e('Order details', 'acadia'); ?>
    </h4>

    <div class="tp-order-info-list">
        <ul>

            <!-- header -->
            <li class="tp-order-info-list-header">
                <h4><?php esc_html_e('Product', 'acadia'); ?></h4>
                <h4><?php esc_html_e('Total', 'acadia'); ?></h4>
            </li>

            <?php
            do_action('woocommerce_order_details_before_order_table_items', $order);

            foreach ($order_items as $item_id => $item) {
                $product = $item->get_product();

                wc_get_template(
                    'order/order-details-item.php',
                    array(
                        'order' => $order,
                        'item_id' => $item_id,
                        'item' => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note' => $product ? $product->get_purchase_note() : '',
                        'product' => $product,
                    )
                );
            }

            do_action('woocommerce_order_details_after_order_table_items', $order);
            ?>

            <?php foreach ($order->get_order_item_totals() as $key => $total):

                if ('cart_subtotal' == $key) {
                    $class = 'tp-order-info-list-subtotal';
                } elseif ('discount' == $key) {
                    $class = 'tp-order-info-list-discount';
                } elseif ('payment_method' == $key) {
                    $class = 'tp-order-info-list-payment';
                } elseif ('shipping' == $key) {
                    $class = 'tp-order-info-list-shipping';
                } else {
                    $class = 'tp-order-info-list-total';
                }

                // Skip 'cart_subtotal' so it won't be displayed
                if ('cart_subtotal' == $key) {
                    continue;
                }
                ?>

                <li class="<?php echo esc_attr($class); ?>">
                    <span><?php echo esc_html($total['label']); ?></span>
                    <span><?php echo acadia_kses($total['value']); ?></span>
                </li>
            <?php endforeach; ?>

            <?php if ($order->get_customer_note()): ?>
                <li class="tp-order-info-list-note">
                    <span><?php esc_html_e('Note:', 'acadia'); ?></span>
                    <p><?php echo acadia_kses(nl2br(wptexturize($order->get_customer_note()))); ?></p>
                </li>
            <?php endif; ?>


        </ul>
    </div>
    <?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</div>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action('woocommerce_after_order_details', $order);

