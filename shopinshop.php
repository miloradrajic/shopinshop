<?php
/**
 * Plugin Name: Shop in Shop TP
 * Plugin URI: https://tp.rs/
 * Description: A WooCommerce extension that provides REST API endpoints for vendor product categories
 * Version: 1.0.1
 * Author: Technology Partnership
 * Author URI: https://tp.rs/
 * Text Domain: shopinshop
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

// Define plugin constants
define('SHOPINSHOP_VERSION', '1.0.0');
define('SHOPINSHOP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SHOPINSHOP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  return;
}

// Include the main plugin class
require_once SHOPINSHOP_PLUGIN_DIR . 'includes/class-shopinshop.php';

/**
 * Initialize the plugin
 */
function shopinshop_init()
{
  ShopInShop::get_instance();
}
add_action('plugins_loaded', 'shopinshop_init');