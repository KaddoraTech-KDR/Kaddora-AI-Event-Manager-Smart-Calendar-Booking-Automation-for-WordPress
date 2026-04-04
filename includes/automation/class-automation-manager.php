<?php
if (!defined('ABSPATH')) exit;

class KAEM_Automation_Manager
{
  public function init()
  {
    add_action('kaem_daily_recurring_check', [$this, 'process_recurring']);

    if (!wp_next_scheduled('kaem_daily_recurring_check')) {
      wp_schedule_event(time(), 'daily', 'kaem_daily_recurring_check');
    }
  }

  public function process_recurring()
  {
    $today = date('Y-m-d');

    $events = get_posts([
      'post_type'      => 'kaem_event',
      'posts_per_page' => -1,
      'post_status'    => 'publish'
    ]);

    foreach ($events as $event) {

      $repeat = get_post_meta($event->ID, '_kaem_repeat', true);
      $date   = get_post_meta($event->ID, '_kaem_date', true);
      $last_generated = get_post_meta($event->ID, '_kaem_last_generated', true);

      if (!$repeat || !$date) continue;

      $next_date = '';

      if ($repeat === 'daily') {
        $next_date = date('Y-m-d', strtotime($date . ' +1 day'));
      } elseif ($repeat === 'weekly') {
        $next_date = date('Y-m-d', strtotime($date . ' +7 days'));
      } elseif ($repeat === 'monthly') {
        $next_date = date('Y-m-d', strtotime($date . ' +1 month'));
      }

      if (!$next_date) continue;

      if ($last_generated === $next_date) continue;

      if ($today !== $next_date) continue;

      $existing = get_posts([
        'post_type'      => 'kaem_event',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => [
          [
            'key'   => '_kaem_date',
            'value' => $next_date,
            'compare' => '='
          ]
        ],
        's' => $event->post_title . ' (Recurring)'
      ]);

      if (!empty($existing)) continue;

      $new_event = [
        'post_title'  => $event->post_title . ' (Recurring)',
        'post_type'   => 'kaem_event',
        'post_status' => 'publish'
      ];

      $new_id = wp_insert_post($new_event);

      if ($new_id && !is_wp_error($new_id)) {
        update_post_meta($new_id, '_kaem_date', $next_date);
        update_post_meta($new_id, '_kaem_time', get_post_meta($event->ID, '_kaem_time', true));
        update_post_meta($new_id, '_kaem_venue', get_post_meta($event->ID, '_kaem_venue', true));
        update_post_meta($new_id, '_kaem_organizer', get_post_meta($event->ID, '_kaem_organizer', true));
        update_post_meta($new_id, '_kaem_repeat', $repeat);

        update_post_meta($event->ID, '_kaem_last_generated', $next_date);
      }
    }
  }
}
