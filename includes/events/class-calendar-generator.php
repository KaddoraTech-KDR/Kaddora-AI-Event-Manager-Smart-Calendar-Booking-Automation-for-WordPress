<?php
if (!defined('ABSPATH')) exit;

class KAEM_Calendar_Generator
{
  public function init()
  {
    add_action('wp_ajax_kaem_load_calendar', [$this, 'ajax_load_calendar']);
    add_action('wp_ajax_nopriv_kaem_load_calendar', [$this, 'ajax_load_calendar']);

    add_action('wp_ajax_kaem_get_event', [$this, 'ajax_get_event']);
    add_action('wp_ajax_nopriv_kaem_get_event', [$this, 'ajax_get_event']);

    add_action('wp_ajax_kaem_save_booking', [$this, 'ajax_save_booking']);
    add_action('wp_ajax_nopriv_kaem_save_booking', [$this, 'ajax_save_booking']);
  }

  // ajax_save_booking
  public function ajax_save_booking()
  {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kaem_nonce')) {
      wp_die('Security check failed');
    }

    if (!KAEM_Helpers::is_pro()) {
      wp_die("Upgrade to Pro");
    }

    // if (!KAEM_License::is_valid()) {
    //   wp_die("Enter License Key");
    // }

    if (!get_option('kaem_pro_mode')) {
      wp_send_json_error("Upgrade to Pro for booking");
    }

    if (!get_option('kaem_enable_booking')) {
      wp_send_json_error("Booking is disabled");
    }

    $event_id = intval($_POST['event_id']);
    $name  = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);

    if (!$event_id || !$name || !$email) {
      echo "❌ Invalid data";
      wp_die();
    }

    if (!is_email($email)) {
      echo "❌ Invalid email";
      wp_die();
    }

    $bookings = get_post_meta($event_id, '_kaem_bookings', true);

    if (!is_array($bookings)) {
      $bookings = [];
    }

    $date = get_post_meta($event_id, '_kaem_date', true);
    $time = get_post_meta($event_id, '_kaem_time', true);

    $bookings[] = [
      'name' => $name,
      'email' => $email,
      'time' => $date . ' ' . $time
    ];

    update_post_meta($event_id, '_kaem_bookings', $bookings);

    wp_mail(
      get_option('admin_email'),
      "New Event Booking",
      "New booking received:\n\nName: $name\nEmail: $email\nEvent ID: $event_id"
    );

    echo "✅ Booking Successful!";
    wp_die();
  }

  // ajax_get_event
  public function ajax_get_event()
  {
    if (!isset($_POST['event_id'])) {
      wp_die('Invalid request');
    }

    $id = intval($_POST['event_id']);

    $title = get_the_title($id);
    $date  = get_post_meta($id, '_kaem_date', true);
    $time  = get_post_meta($id, '_kaem_time', true);
    $venue = get_post_meta($id, '_kaem_venue', true);
    $organizer = get_post_meta($id, '_kaem_organizer', true);

    echo "<h3>" . esc_html($title) . "</h3>";
    echo "<p><strong>Date:</strong> " . esc_html($date) . "</p>";
    echo "<p><strong>Time:</strong> " . esc_html($time) . "</p>";
    echo "<p><strong>Venue:</strong> " . esc_html($venue) . "</p>";
    echo "<p><strong>Organizer:</strong> " . esc_html($organizer) . "</p>";

    echo "<button class='kaem-book-btn' data-id='$id'>Book Now</button>";

    echo "<div id='kaem-booking-form' style='display:none; margin-top:10px;'>
          <input type='text' id='kaem_name' placeholder='Your Name'><br><br>
          <input type='email' id='kaem_email' placeholder='Your Email'><br><br>
          <button id='kaem-submit-booking' data-id='$id'>Submit</button>
        </div>";

    echo "<div id='kaem-booking-message'></div>";

    wp_die();
  }

  // ajax_load_calendar
  public function ajax_load_calendar()
  {
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('m');
    $year  = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

    $data = $this->generate_custom($month, $year);

    include KAEM_PLUGIN_DIR . 'templates/calendar/calendar-month.php';

    wp_die();
  }

  // generate_custom
  public function generate_custom($month, $year)
  {
    $args = [
      'post_type' => 'kaem_event',
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'meta_key' => '_kaem_date',
      'orderby' => 'meta_value',
      'order' => 'ASC'
    ];

    $query = new WP_Query($args);
    $events = [];

    while ($query->have_posts()) {
      $query->the_post();

      $id = get_the_ID();
      $date = get_post_meta($id, '_kaem_date', true);

      if (!empty($date)) {
        $events[$date][] = [
          'id'    => $id,
          'title' => get_the_title(),
        ];
      }
    }

    wp_reset_postdata();

    return [
      'month'  => $month,
      'year'   => $year,
      'events' => $events
    ];
  }

  // generate
  public function generate()
  {
    $month = date('m');
    $year  = date('Y');

    return $this->generate_custom($month, $year);
  }
}
