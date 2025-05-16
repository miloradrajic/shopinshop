<?php
/**
 * Main plugin class
 *
 * @package ShopInShop
 */

if (!defined('WPINC')) {
  die;
}

class ShopInShop
{
  /**
   * Plugin instance.
   *
   * @var ShopInShop
   */
  private static $instance = null;

  /**
   * Get plugin instance.
   *
   * @return ShopInShop
   */
  public static function get_instance()
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Constructor
   */
  private function __construct()
  {
    add_action('rest_api_init', array($this, 'register_rest_routes'));
  }

  /**
   * Register REST API routes
   */
  public function register_rest_routes()
  {
    register_rest_route('kopa/v1', '/shopinshop/categories/vendor/(?P<id>[a-zA-Z0-9-_]+)', array(
      'methods' => 'GET',
      'callback' => array($this, 'get_vendor_product_categories'),
      'permission_callback' => array($this, 'validate_woocommerce_api_auth'),
      'args' => [
        'id' => [
          'required' => true,
          'validate_callback' => function ($param) {
            return is_string($param);
          },
        ],
      ]
    ));

    register_rest_route('kopa/v1', '/shopinshop/categories/list', array(
      'methods' => 'GET',
      'callback' => array($this, 'get_product_categories'),
      'permission_callback' => array($this, 'validate_woocommerce_api_auth'),
    ));
  }

  /**
   * Validate WooCommerce API authentication
   *
   * @param WP_REST_Request $request
   * @return bool|WP_Error
   */
  public function validate_woocommerce_api_auth($request)
  {
    if (!class_exists('WooCommerce')) {
      return new WP_Error('woocommerce_not_found', __('WooCommerce is not active'), array('status' => 403));
    }

    $consumer_key = $request->get_param('consumer_key');
    $consumer_secret = $request->get_param('consumer_secret');

    if (!$consumer_key || !$consumer_secret) {
      return new WP_Error('rest_forbidden', __('Missing consumer key or secret'), array('status' => 403));
    }

    global $wpdb;
    $key_data = $wpdb->get_row($wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE consumer_key = %s",
      wc_api_hash($consumer_key)
    ));

    if (!$key_data || !hash_equals($key_data->consumer_secret, $consumer_secret)) {
      return new WP_Error('rest_forbidden', __('Invalid consumer key or secret'), array('status' => 403));
    }

    $user = get_user_by('id', $key_data->user_id);
    if (!$user || (!in_array('customer', $user->roles) && !in_array('administrator', $user->roles))) {
      return new WP_Error('rest_forbidden', __('User does not have permission to access this endpoint'), array('status' => 403));
    }

    return true;
  }

  /**
   * Get vendor product categories
   *
   * @param WP_REST_Request $data
   * @return WP_REST_Response
   */
  public function get_vendor_product_categories($data)
  {
    $store_url = $data->get_param('id');
    $category_data = [];

    $user = get_user_by('slug', $store_url);

    if (!$user) {
      return new WP_REST_Response(['error' => 'User not found'], 404);
    }

    $query = new WP_Query([
      'post_type' => 'product',
      'post_status' => ['publish', 'pending'],
      'author' => $user->ID,
      'posts_per_page' => -1,
      'fields' => 'ids',
    ]);

    if ($query->have_posts()) {
      foreach ($query->posts as $product_id) {
        $categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'all']);

        foreach ($categories as $category) {
          if ($category->parent == 0) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : null;

            $category_data[$category->term_id] = [
              'name' => $category->name,
              'handle' => strtolower($category->name),
              'image' => $image_url,
            ];
          }
        }
      }
    }

    wp_reset_postdata();

    return new WP_REST_Response(array_values($category_data), 200);
  }

  /**
   * Get all product categories
   *
   * @return WP_REST_Response
   */
  public function get_product_categories()
  {
    $category_data = [];

    $args = array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'parent' => 0,
    );

    $categories = get_terms($args);

    if (!empty($categories) && !is_wp_error($categories)) {
      foreach ($categories as $category) {
        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
        $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : null;

        $category_data[$category->term_id] = [
          'name' => $category->name,
          'handle' => strtolower($category->name),
          'image' => $image_url,
        ];
      }
    }

    return new WP_REST_Response(array_values($category_data), 200);
  }
}