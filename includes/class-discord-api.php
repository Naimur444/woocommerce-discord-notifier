<?php
namespace WCDiscordNotifier;

class DiscordAPI {
    public function sendMessage(string $webhook_url, string $content): void {
        $response = wp_remote_post($webhook_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode(['content' => $content]),
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 204) {
            throw new \Exception(
                sprintf(
                    __('Discord API error: %s', 'wc-discord-notifier'),
                    wp_remote_retrieve_response_message($response)
                )
            );
        }
    }
}