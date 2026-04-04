<?php
if (!defined('ABSPATH')) exit;

class KAEM_Reminder_Scheduler
{
  public function init()
  {
    add_action('kaem_daily_event_check', [$this, 'send_reminders']);

    // Schedule event if not already
    if (!wp_next_scheduled('kaem_daily_event_check')) {
      wp_schedule_event(time(), 'daily', 'kaem_daily_event_check');
    }
  }

  public function send_reminders()
  {
    $today = date('Y-m-d');

    $args = [
      'post_type' => 'kaem_event',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    ];

    $events = get_posts($args);

    foreach ($events as $event) {
      $event_date = get_post_meta($event->ID, '_kaem_date', true);

      // Reminder 1 day before
      $reminder_date = date('Y-m-d', strtotime($event_date . ' -1 day'));

      if ($today === $reminder_date) {
        $bookings = get_post_meta($event->ID, '_kaem_bookings', true);

        if (!is_array($bookings)) continue;

        foreach ($bookings as $booking) {
          $email = sanitize_email($booking['email']);
          $name  = sanitize_text_field($booking['name']);

          $subject = "Reminder: Upcoming Event";
          $message = "Hi $name,\n\nThis is a reminder for your event:\n" . get_the_title($event->ID);

          wp_mail($email, $subject, $message);
        }
      }
    }
  }
}
