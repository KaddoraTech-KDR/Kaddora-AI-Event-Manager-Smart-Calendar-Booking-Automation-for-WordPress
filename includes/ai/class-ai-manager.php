<?php
if (!defined('ABSPATH')) exit;

class KAEM_AI_Manager
{
  public function init()
  {
    add_action('wp_ajax_kaem_ai_generate', [$this, 'generate']);
  }

  public function generate()
  {
    if (!KAEM_Helpers::is_pro()) {
      wp_die("Upgrade to Pro");
    }

    // if (!KAEM_License::is_valid()) {
    //   wp_die("Enter License Key");
    // }

    $type = sanitize_text_field($_POST['type']);
    $title = sanitize_text_field($_POST['title']);

    if (!get_option('kaem_enable_ai')) {
      wp_send_json_error("AI feature is disabled");
    }

    if (!$title) {
      echo "Enter event title first";
      wp_die();
    }

    $prompt = "";

    if ($type === "desc") {
      $prompt = "Write a professional event description for: $title";
    }

    if ($type === "seo") {
      $prompt = "Generate SEO optimized title for event: $title";
    }

    if ($type === "tags") {
      $prompt = "Generate tags for event: $title";
    }

    $api_key = defined('KAEM_AI_API_KEY') ? KAEM_AI_API_KEY : '';

    if (empty($api_key)) {

      if ($type === "desc") {
        echo "Join us for $title, an exciting event designed for learning, networking, and growth.";
      }

      if ($type === "seo") {
        echo "$title | Best Event | Book Now";
      }

      if ($type === "tags") {
        echo strtolower($title) . ", event, booking, conference, meetup";
      }

      wp_die();
    }

    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', [
      'headers' => [
        'Content-Type'  => 'application/json',
        'Authorization' => 'Bearer ' . $api_key,
        'HTTP-Referer' => home_url(),
        'X-Title' => 'Kaddora AI Event Manager'
      ],
      'body' => json_encode([
        'model' => 'mistralai/mistral-7b-instruct',
        'messages' => [
          [
            'role' => 'user',
            'content' => $prompt
          ]
        ]
      ])
    ]);

    if (is_wp_error($response)) {
      echo "API Error";
      wp_die();
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    echo $body['choices'][0]['message']['content'] ?? "No response";

    wp_die();
  }
}
