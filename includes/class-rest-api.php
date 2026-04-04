<?php
if (!defined('ABSPATH')) exit;

class KAEM_REST_API
{
  public function init()
  {
    add_action('rest_api_init', [$this, 'register_routes']);
  }

  public function register_routes()
  {
    // Secure Events API
    register_rest_route('kaem/v1', '/events', [
      'methods'  => 'GET',
      'callback' => [$this, 'get_events'],
      'permission_callback' => [$this, 'verify_token']
    ]);

    // Public Booking API (optional secure later)
    register_rest_route('kaem/v1', '/book', [
      'methods'  => 'POST',
      'callback' => [$this, 'create_booking'],
      'permission_callback' => '__return_true'
    ]);
  }

  // TOKEN VERIFY (Improved)
  public function verify_token($request)
  {
    error_log($request->get_header('x-kaem-token') ?? 'NO TOKEN');
    $token = $request->get_header('x-kaem-token');
    error_log('Token: ' . ($token ? $token : 'MISSING'));
    $saved = get_option('kaem_api_token');

    if (!$token || !$saved) {
      return new WP_Error(
        'no_token',
        'API token missing',
        ['status' => 403]
      );
    }

    if ($token !== $saved) {
      return new WP_Error(
        'invalid_token',
        'Invalid API token',
        ['status' => 403]
      );
    }

    return true;
  }

  // GET EVENTS (Pagination + Filter + Clean)
  public function get_events($request)
  {
    $page     = max(1, intval($request->get_param('page')));
    $per_page = max(1, intval($request->get_param('per_page')));
    $search   = sanitize_text_field($request->get_param('search'));
    $date     = sanitize_text_field($request->get_param('date'));

    $args = [
      'post_type'      => 'kaem_event',
      'posts_per_page' => $per_page,
      'paged'          => $page,
    ];

    // Search filter
    if ($search) {
      $args['s'] = $search;
    }

    // Date filter
    if ($date) {
      $args['meta_query'] = [
        [
          'key'   => '_kaem_date',
          'value' => $date,
          'compare' => '='
        ]
      ];
    }

    $query = new WP_Query($args);

    $data = [];

    foreach ($query->posts as $event) {

      $event_date = get_post_meta($event->ID, '_kaem_date', true);

      if (!$event_date) continue; // clean API

      $data[] = [
        'id'    => $event->ID,
        'title' => $event->post_title,
        'date'  => $event_date,
        'link'  => get_permalink($event->ID)
      ];
    }

    return rest_ensure_response([
      'success' => true,
      'total'   => $query->found_posts,
      'page'    => $page,
      'per_page' => $per_page,
      'data'    => $data
    ]);
  }

  // CREATE BOOKING
  public function create_booking(WP_REST_Request $request)
  {
    $event_id = intval($request->get_param('event_id'));
    $name     = sanitize_text_field($request->get_param('name'));
    $email    = sanitize_email($request->get_param('email'));

    if (!$event_id || !$name || !$email) {
      return new WP_Error('invalid_data', 'Missing required fields', ['status' => 400]);
    }

    if (get_post_type($event_id) !== 'kaem_event') {
      return new WP_Error('invalid_event', 'Event not found', ['status' => 404]);
    }

    $bookings = get_post_meta($event_id, '_kaem_bookings', true);

    if (!is_array($bookings)) {
      $bookings = [];
    }

    $bookings[] = [
      'name'  => $name,
      'email' => $email,
      'time'  => current_time('mysql')
    ];

    update_post_meta($event_id, '_kaem_bookings', $bookings);

    return rest_ensure_response([
      'success'  => true,
      'message'  => 'Booking created successfully',
      'event_id' => $event_id
    ]);
  }
}
