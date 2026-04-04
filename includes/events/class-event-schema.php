<?php
if (!defined('ABSPATH')) exit;

class KAEM_Event_Schema
{
  public function init()
  {
    add_action('wp_head', function () {
      if (is_singular('kaem_event')) {
        $this->add_schema(get_the_ID());
      }
    });
  }

  public function add_schema($post_id)
  {
    if (get_post_type($post_id) !== 'kaem_event') return;

    $date = get_post_meta($post_id, '_kaem_date', true);
    $venue = get_post_meta($post_id, '_kaem_venue', true);

    if (empty($date) || empty($venue)) return; // safety

    $schema = [
      "@context" => "https://schema.org",
      "@type" => "Event",
      "name" => get_the_title($post_id),
      "startDate" => $date,
      "location" => [
        "@type" => "Place",
        "name" => $venue
      ]
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
  }
}
