<?php
namespace WCDiscordNotifier;

class OrderHandler {
    private static $instance = null;
    
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Trigger for any order status change
        add_action('woocommerce_order_status_changed', [$this, 'handleOrderStatusChange'], 10, 3);
    }

    public function handleOrderStatusChange($order_id, $old_status, $new_status): void {
		try {
			$order = wc_get_order($order_id);
			if (!$order) {
				throw new \Exception('Invalid order ID');
			}

			$settings = Settings::getInstance()->getSettings();
			if (empty($settings['webhooks'])) {
				return;
			}

			// Strip HTML tags and decode entities from total
			$total = wp_strip_all_tags($order->get_formatted_order_total());
			$total = html_entity_decode($total);

			// Format message with clean values
			$message = str_replace(
				['{order_id}', '{total}', '{customer_name}', '{status}'],
				[
					$order->get_id(),
					$total,
					$order->get_formatted_billing_full_name(),
					wc_get_order_status_name($new_status)
				],
				$settings['message_template']
			);

			$discord = new DiscordAPI();
			foreach ($settings['webhooks'] as $webhook) {
				$discord->sendMessage($webhook, $message);
			}
		} catch (\Exception $e) {
			error_log(sprintf('Discord notification failed for order #%d: %s', $order_id, $e->getMessage()));
		}
	}
}