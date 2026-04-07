-- Chawkbazar Seed Data (INSERT statements only)
INSERT INTO `address` (`id`, `title`, `type`, `default`, `address`, `location`, `customer_id`, `created_at`, `updated_at`) VALUES
INSERT INTO `attachments` (`id`, `url`, `created_at`, `updated_at`) VALUES
INSERT INTO `attributes` (`id`, `slug`, `language`, `name`, `shop_id`, `created_at`, `updated_at`) VALUES
INSERT INTO `attribute_product` (`id`, `attribute_value_id`, `product_id`, `created_at`, `updated_at`) VALUES
INSERT INTO `attribute_values` (`id`, `slug`, `attribute_id`, `value`, `language`, `meta`, `created_at`, `updated_at`) VALUES
INSERT INTO `balances` (`id`, `shop_id`, `admin_commission_rate`, `total_earnings`, `withdrawn_amount`, `current_balance`, `is_custom_commission`, `payment_info`, `created_at`, `updated_at`) VALUES
INSERT INTO `became_sellers` (`id`, `page_options`, `language`, `created_at`, `updated_at`) VALUES
INSERT INTO `categories` (`id`, `name`, `slug`, `language`, `icon`, `image`, `banner_image`, `details`, `parent`, `created_at`, `updated_at`, `deleted_at`) VALUES
INSERT INTO `category_product` (`id`, `product_id`, `category_id`) VALUES
INSERT INTO `commissions` (`id`, `level`, `sub_level`, `description`, `min_balance`, `max_balance`, `commission`, `image`, `language`, `created_at`, `updated_at`) VALUES
INSERT INTO `coupons` (`id`, `code`, `language`, `description`, `image`, `type`, `amount`, `minimum_cart_amount`, `active_from`, `expire_at`, `target`, `is_approve`, `shop_id`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
INSERT INTO `faqs` (`id`, `user_id`, `shop_id`, `faq_title`, `slug`, `faq_description`, `faq_type`, `issued_by`, `language`, `deleted_at`, `created_at`, `updated_at`) VALUES
INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `generated_conversions`, `custom_properties`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `generated_conversions`, `custom_properties`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
INSERT INTO `orders` (`id`, `tracking_number`, `customer_id`, `customer_contact`, `customer_name`, `amount`, `sales_tax`, `paid_total`, `total`, `note`, `cancelled_amount`, `cancelled_tax`, `cancelled_delivery_fee`, `language`, `coupon_id`, `parent_id`, `shop_id`, `discount`, `payment_gateway`, `altered_payment_gateway`, `shipping_address`, `billing_address`, `logistics_provider`, `delivery_fee`, `delivery_time`, `order_status`, `payment_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
INSERT INTO `order_product` (`id`, `order_id`, `product_id`, `variation_option_id`, `order_quantity`, `unit_price`, `subtotal`, `deleted_at`, `created_at`, `updated_at`) VALUES
INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `type_id`, `price`, `shop_id`, `sale_price`, `language`, `min_price`, `max_price`, `sku`, `quantity`, `sold_quantity`, `in_stock`, `is_taxable`, `in_flash_sale`, `shipping_class_id`, `status`, `visibility`, `product_type`, `unit`, `height`, `width`, `length`, `image`, `video`, `gallery`, `deleted_at`, `created_at`, `updated_at`, `author_id`, `manufacturer_id`, `is_digital`, `is_external`, `external_product_url`, `external_product_button_text`, `blocked_dates`) VALUES
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `type_id`, `price`, `shop_id`, `sale_price`, `language`, `min_price`, `max_price`, `sku`, `quantity`, `sold_quantity`, `in_stock`, `is_taxable`, `in_flash_sale`, `shipping_class_id`, `status`, `visibility`, `product_type`, `unit`, `height`, `width`, `length`, `image`, `video`, `gallery`, `deleted_at`, `created_at`, `updated_at`, `author_id`, `manufacturer_id`, `is_digital`, `is_external`, `external_product_url`, `external_product_button_text`, `blocked_dates`) VALUES
INSERT INTO `product_tag` (`id`, `product_id`, `tag_id`) VALUES
INSERT INTO `refund_policies` (`id`, `title`, `slug`, `description`, `target`, `language`, `status`, `shop_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
INSERT INTO `refund_reasons` (`id`, `name`, `slug`, `language`, `created_at`, `updated_at`, `deleted_at`) VALUES
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
INSERT INTO `settings` (`id`, `options`, `language`, `created_at`, `updated_at`) VALUES
INSERT INTO `shipping_classes` (`id`, `name`, `amount`, `is_global`, `type`, `created_at`, `updated_at`) VALUES
INSERT INTO `shops` (`id`, `owner_id`, `name`, `slug`, `description`, `cover_image`, `logo`, `is_active`, `address`, `settings`, `notifications`, `created_at`, `updated_at`) VALUES
INSERT INTO `tags` (`id`, `name`, `slug`, `language`, `icon`, `image`, `details`, `created_at`, `updated_at`, `deleted_at`) VALUES
INSERT INTO `tax_classes` (`id`, `country`, `state`, `zip`, `city`, `rate`, `name`, `is_global`, `priority`, `on_shipping`, `created_at`, `updated_at`) VALUES
INSERT INTO `terms_and_conditions` (`id`, `user_id`, `shop_id`, `title`, `slug`, `description`, `type`, `issued_by`, `is_approved`, `language`, `deleted_at`, `created_at`, `updated_at`) VALUES
INSERT INTO `types` (`id`, `name`, `settings`, `slug`, `language`, `icon`, `promotional_sliders`, `images`, `created_at`, `updated_at`) VALUES
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_active`, `shop_id`) VALUES
INSERT INTO `user_profiles` (`id`, `avatar`, `bio`, `socials`, `contact`, `notifications`, `customer_id`, `created_at`, `updated_at`) VALUES
INSERT INTO `variation_options` (`id`, `title`, `image`, `price`, `sale_price`, `language`, `quantity`, `sold_quantity`, `is_disable`, `sku`, `options`, `product_id`, `digital_file_tracker`, `created_at`, `updated_at`, `is_digital`) VALUES
INSERT INTO `wallets` (`id`, `total_points`, `points_used`, `available_points`, `customer_id`, `created_at`, `updated_at`) VALUES
INSERT INTO `withdraws` (`id`, `shop_id`, `amount`, `payment_method`, `status`, `details`, `note`, `deleted_at`, `created_at`, `updated_at`) VALUES
