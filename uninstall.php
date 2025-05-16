<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package ShopInShop
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit;
}

// Clean up any plugin-specific data here if needed
// For example, delete options, custom tables, etc. 