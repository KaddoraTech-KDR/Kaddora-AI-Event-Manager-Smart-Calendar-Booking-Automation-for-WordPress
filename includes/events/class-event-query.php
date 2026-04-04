<?php
if (!defined('ABSPATH')) exit;

class KAEM_Event_Query
{
  public function get_events($args = [])
  {
    $default = [
      'post_type' => 'kaem_event',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    ];

    $args = wp_parse_args($args, $default);

    return new WP_Query($args);
  }

  public function get_upcoming_events()
  {
    return $this->get_events([
      'meta_key' => '_kaem_date',
      'orderby' => 'meta_value',
      'order' => 'ASC'
    ]);
  }
}
