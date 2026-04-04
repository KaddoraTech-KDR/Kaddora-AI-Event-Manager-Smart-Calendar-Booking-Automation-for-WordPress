<?php
if (!defined('ABSPATH')) exit;

class KAEM_WooCommerce
{
  public function init()
  {
    add_action('init', [$this, 'check_woocommerce']);
  }

  public function check_woocommerce()
  {
    if (!class_exists('WooCommerce')) return;

    // Example hook
    add_action('woocommerce_thankyou', [$this, 'after_purchase']);
  }

  public function after_purchase($order_id)
  {
    // future: link booking with order
    error_log("Order completed: " . $order_id);
  }
}
