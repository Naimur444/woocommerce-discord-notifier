<?php
/**
 * Plugin Name: WooCommerce Discord Notifier
 * Plugin URI: https://facebook.com/naimur444
 * Description: Sends WooCommerce order notifications to Discord channels with customizable message templates and multi-webhook support.
 * Version: 1.0.0
 * Author: Naimur Rahman Sarker
 * Author URI: https://facebook.com/naimur444
 * Text Domain: wc-discord-notifier
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * WC requires at least: 7.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


declare(strict_types=1);

namespace WCDiscordNotifier;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin {
    private static $instance = null;
    
    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->defineConstants();
        $this->loadDependencies();
        $this->initHooks();
    }

    private function defineConstants(): void {
        define('WCDN_VERSION', '1.0.0');
        define('WCDN_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WCDN_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    private function loadDependencies(): void {
        require_once WCDN_PLUGIN_DIR . 'includes/class-settings.php';
        require_once WCDN_PLUGIN_DIR . 'includes/class-discord-api.php';
        require_once WCDN_PLUGIN_DIR . 'includes/class-order-handler.php';
    }

    private function initHooks(): void {
        add_action('plugins_loaded', [$this, 'checkDependencies']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function checkDependencies(): void {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>' . 
                     esc_html__('WooCommerce Discord Notifier requires WooCommerce to be installed and active.', 'wc-discord-notifier') . 
                     '</p></div>';
            });
            return;
        }
        
        Settings::getInstance();
        OrderHandler::getInstance();
    }

    public function enqueueAdminAssets(): void {
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'wc-discord-notifier') !== false) {
            wp_enqueue_style(
                'wcdn-admin',
                WCDN_PLUGIN_URL . 'assets/css/admin.css',
                [],
                WCDN_VERSION
            );

            wp_enqueue_script(
                'wcdn-admin',
                WCDN_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                WCDN_VERSION,
                true
            );

            wp_localize_script('wcdn-admin', 'wcdnAdmin', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wcdn-admin'),
                'i18n' => [
                    'testSuccess' => __('Test notification sent successfully!', 'wc-discord-notifier'),
                    'testError' => __('Failed to send test notification.', 'wc-discord-notifier'),
                ]
            ]);
        }
    }
}

Plugin::getInstance();

