<?php
namespace WCDiscordNotifier;

class Settings {
    private static $instance = null;
    private $option_name = 'wcdn_settings';
    
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('wp_ajax_wcdn_test_webhook', [$this, 'testWebhook']);
    }

    public function addSettingsPage(): void {
        add_submenu_page(
            'woocommerce',
            __('Discord Notifier', 'wc-discord-notifier'),
            __('Discord Notifier', 'wc-discord-notifier'),
            'manage_options',
            'wc-discord-notifier',
            [$this, 'renderSettingsPage']
        );
    }

    public function registerSettings(): void {
        register_setting('wcdn_settings', $this->option_name, [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitizeSettings']
        ]);

        add_settings_section(
            'wcdn_webhooks',
            __('Discord Webhooks', 'wc-discord-notifier'),
            [$this, 'renderWebhooksSection'],
            'wc-discord-notifier'
        );

        add_settings_field(
            'webhooks',
            __('Webhook URLs', 'wc-discord-notifier'),
            [$this, 'renderWebhooksField'],
            'wc-discord-notifier',
            'wcdn_webhooks'
        );

        add_settings_field(
            'message_template',
            __('Message Template', 'wc-discord-notifier'),
            [$this, 'renderMessageTemplateField'],
            'wc-discord-notifier',
            'wcdn_webhooks'
        );
    }

    public function renderSettingsPage(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap wcdn-settings">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('wcdn_settings');
                do_settings_sections('wc-discord-notifier');
                submit_button();
                ?>
                
                <button type="button" class="wcdiscord-btn wcdiscord-btn-secondary" id="wcdn-test-webhook">
                    <?php esc_html_e('Send Test Notification', 'wc-discord-notifier'); ?>
                </button>
            </form>

            <!-- Template for new webhook fields -->
            <script type="text/template" id="wcdn-webhook-template">
                <div class="wcdn-webhook-row">
                    <input type="url" name="wcdn_settings[webhooks][]" class="wcdn-webhook-input" required />
                    <button type="button" class="wcdiscord-btn wcdiscord-btn-danger wcdn-remove-webhook">
                        <?php esc_html_e('Remove', 'wc-discord-notifier'); ?>
                    </button>
                </div>
            </script>
        </div>
        <?php
    }

    public function renderWebhooksSection(): void {
        echo '<p>' . esc_html__('Configure your Discord webhook URLs below.', 'wc-discord-notifier') . '</p>';
    }

    public function renderWebhooksField(): void {
        $settings = $this->getSettings();
        ?>
        <div id="wcdn-webhooks">
            <?php 
            if (!empty($settings['webhooks'])) {
                foreach ($settings['webhooks'] as $webhook): 
            ?>
                <div class="wcdn-webhook-row">
                    <input 
                        type="url" 
                        name="wcdn_settings[webhooks][]" 
                        value="<?php echo esc_url($webhook); ?>" 
                        class="wcdn-webhook-input"
                        required
                    />
                    <button type="button" class="wcdiscord-btn wcdiscord-btn-danger wcdn-remove-webhook">
                        <?php esc_html_e('Remove', 'wc-discord-notifier'); ?>
                    </button>
                </div>
            <?php 
                endforeach;
            } else {
                // Add one empty field if no webhooks exist
                ?>
                <div class="wcdn-webhook-row">
                    <input 
                        type="url" 
                        name="wcdn_settings[webhooks][]" 
                        class="wcdn-webhook-input"
                        required
                    />
                    <button type="button" class="wcdiscord-btn wcdiscord-btn-danger wcdn-remove-webhook">
                        <?php esc_html_e('Remove', 'wc-discord-notifier'); ?>
                    </button>
                </div>
            <?php
            }
            ?>
        </div>
        <button type="button" class="wcdiscord-btn wcdiscord-btn-secondary" id="wcdn-add-webhook">
            <?php esc_html_e('Add Webhook', 'wc-discord-notifier'); ?>
        </button>
        <?php
    }

    public function renderMessageTemplateField(): void {
        $settings = $this->getSettings();
        ?>
        <textarea 
            name="wcdn_settings[message_template]" 
            class="large-text" 
            rows="5"
        ><?php echo esc_textarea($settings['message_template']); ?></textarea>
        <p class="description">
            <?php esc_html_e('Available placeholders: {order_id}, {total}, {customer_name}, {status}', 'wc-discord-notifier'); ?>
        </p>
        <?php
    }

    private function getDefaultSettings(): array {
        return [
            'webhooks' => [],
            'message_template' => "🛍️ Order #{order_id} - Status: {status}\n" .
                                "💰 Total: {total}\n" .
                                "👤 Customer: {customer_name}"
        ];
    }

    public function getSettings(): array {
        return wp_parse_args(
            get_option($this->option_name, []),
            $this->getDefaultSettings()
        );
    }

    public function sanitizeSettings($input): array {
        $sanitized = [];
        
        if (isset($input['webhooks']) && is_array($input['webhooks'])) {
            $sanitized['webhooks'] = array_map('esc_url_raw', $input['webhooks']);
            // Remove empty webhook URLs
            $sanitized['webhooks'] = array_filter($sanitized['webhooks']);
        }
        
        if (isset($input['message_template'])) {
            $sanitized['message_template'] = sanitize_textarea_field($input['message_template']);
        }
        
        return $sanitized;
    }

    public function testWebhook(): void {
        check_ajax_referer('wcdn-admin');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $settings = $this->getSettings();
        $discord = new DiscordAPI();
        
        try {
            foreach ($settings['webhooks'] as $webhook) {
                $discord->sendMessage($webhook, '🔔 Test notification from WooCommerce Discord Notifier');
            }
            wp_send_json_success();
        } catch (\Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}
