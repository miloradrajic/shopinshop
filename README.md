# Shop in Shop WordPress Plugin

A WooCommerce extension that provides REST API endpoints for vendor product categories.

## Description

This plugin extends WooCommerce functionality by adding custom REST API endpoints for managing vendor product categories. It provides endpoints to:

- Get product categories for a specific vendor
- Get a list of all product categories

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- WooCommerce 3.0 or higher

## Installation

1. Upload the `shopinshop` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce is installed and activated

## API Endpoints

### Get Vendor Categories

```
GET /wp-json/kopa/v1/shopinshop/categories/vendor/{vendor_id}
```

### Get All Categories

```
GET /wp-json/kopa/v1/shopinshop/categories
```

Both endpoints require WooCommerce API authentication using consumer key and secret.

## Security

The plugin implements WooCommerce API authentication to ensure secure access to the endpoints. Users must have valid WooCommerce API credentials to access the endpoints.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.1

- Changed categories endpoint, removed /list slug.

### 1.0.0

- Initial release
- Added REST API endpoints for vendor product categories
- Implemented WooCommerce API authentication
- Added support for getting vendor-specific categories
- Added support for getting all product categories

## Support

For support, please contact [your-email@example.com]
