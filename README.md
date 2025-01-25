=== WooCommerce Discord Notifier ===
Contributors: naimur444  
Donate link: https://facebook.com/naimur444  
Tags: WooCommerce, Discord, Notifications, Webhooks, Order Alerts  
Requires at least: 6.0  
Tested up to: 6.3  
Requires PHP: 7.4  
WC requires at least: 7.0  
WC tested up to: 8.0  
Stable tag: 1.0.0  
License: GPL v2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Send WooCommerce order notifications to Discord channels with customizable messages and multi-webhook support.

== Description ==

**WooCommerce Discord Notifier** is a powerful and user-friendly plugin that allows you to send WooCommerce order notifications directly to Discord channels via webhooks. Keep your team informed about new sales in real time with customizable message templates.

**Features:**
- Send notifications for new WooCommerce orders to Discord.
- Customizable message templates with placeholders like `{order_id}`, `{total}`, and `{customer_name}`.
- Multi-webhook support to notify multiple Discord channels.
- Responsive and modern settings page built with Tailwind CSS.
- Error handling with admin notices and debug logs.
- Fully localized and translatable.

**Use Cases:**
- Get notified instantly when a sale is made.
- Notify your sales or support team about incoming orders.
- Keep a log of WooCommerce orders in a Discord channel.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/woocommerce-discord-notifier/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **WooCommerce > Discord Notifier Settings** to configure the plugin.
4. Add your Discord webhook URL(s) and customize the message template.
5. Save your settings, and you're ready to go!

== Frequently Asked Questions ==

= How do I set up a Discord webhook? =
1. Go to your Discord server.
2. Open the channel where you want to receive notifications.
3. Go to **Channel Settings > Integrations > Webhooks**.
4. Click **Create Webhook**, give it a name, and copy the webhook URL.
5. Paste the webhook URL into the plugin settings.

= Can I send notifications to multiple Discord channels? =
Yes, you can add multiple webhook URLs in the settings. Notifications will be sent to all configured channels.

= What placeholders are available for the message template? =
You can use the following placeholders:
- `{order_id}`: The WooCommerce order ID.
- `{total}`: The total amount of the order.
- `{customer_name}`: The customer's full name.

= Does the plugin work with custom WooCommerce statuses? =
Yes, the plugin can be customized to support custom statuses if needed.

== Screenshots ==

1. Settings page to configure webhooks and message templates.
2. Example of a Discord notification for a WooCommerce order.

== Changelog ==

= 1.0.0 =
* Initial release.
* Send order notifications to Discord via webhooks.
* Customizable message templates.
* Multi-webhook support.
* Responsive admin settings page styled with Tailwind CSS.

== Upgrade Notice ==

= 1.0.0 =
Initial release with essential features for WooCommerce order notifications to Discord.

== License ==

This plugin is licensed under the GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for details.
