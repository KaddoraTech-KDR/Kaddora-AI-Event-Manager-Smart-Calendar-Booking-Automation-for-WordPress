<?php
if (!defined('ABSPATH')) exit;

class KAEM_Meta_Boxes
{
  public function init($loader)
  {
    $loader->add_action('add_meta_boxes', $this, 'add_meta_boxes');
    $loader->add_action('save_post_kaem_event', $this, 'save_meta');
    $loader->add_action('add_meta_boxes', $this, 'add_booking_meta_box');
    $loader->add_action('wp_ajax_kaem_delete_booking', $this, 'delete_booking');
    $loader->add_action('wp_ajax_kaem_export_csv', $this, 'export_csv');
    $loader->add_action('add_meta_boxes', $this, 'add_ai_meta_box');
  }

  // add_ai_meta_box
  public function add_ai_meta_box()
  {
    add_meta_box(
      'kaem_ai_tools_',
      __("AI Tools", "kaddora-ai-event-manager"),
      array($this, "render_ai_box"),
      'kaem_event',
      'normal',
      'high'
    );
  }

  // render_ai_box
  public function render_ai_box($post)
  {
?>
    <button type="button" class="button" id="kaem-generate-desc">
      Generate Description
    </button>

    <button type="button" class="button" id="kaem-generate-seo">
      SEO Title
    </button>

    <button type="button" class="button" id="kaem-generate-tags">
      Suggest Tags
    </button>

    <div id="kaem-ai-result" style="margin-top:10px;"></div>
  <?php
  }

  // export_csv
  public function export_csv()
  {
    $post_id = intval($_GET['post_id']);
    $bookings = get_post_meta($post_id, '_kaem_bookings', true);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bookings.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Name', 'Email', 'Time']);

    foreach ($bookings as $booking) {
      fputcsv($output, [
        $booking['name'],
        $booking['email'],
        $booking['time']
      ]);
    }

    fclose($output);
    exit;
  }

  // delete_booking
  public function delete_booking()
  {
    $index = intval($_POST['index']);
    $post_id = intval($_POST['post_id']);

    $bookings = get_post_meta($post_id, '_kaem_bookings', true);

    if (isset($bookings[$index])) {
      unset($bookings[$index]);
      update_post_meta($post_id, '_kaem_bookings', array_values($bookings));
    }

    echo "Deleted";
    wp_die();
  }

  // add_booking_meta_box
  public function add_booking_meta_box()
  {
    add_meta_box(
      'kaem_event_bookings',
      __('Event Bookings', 'kaddora-ai-event-manager'),
      array($this, "render_booking_meta_box"),
      'kaem_event',
      'normal',
      'default'
    );
  }

  // render_booking_meta_box
  public function render_booking_meta_box($post)
  {
    $bookings = get_post_meta($post->ID, '_kaem_bookings', true);

    if (!is_array($bookings) || empty($bookings)) {
      echo '<p>No bookings found.</p>';
      return;
    }
  ?>
    <a href="<?php echo admin_url('admin-ajax.php?action=kaem_export_csv&post_id=' . $post->ID); ?>" class="button">
      Export CSV
    </a>

    <table class="widefat fixed striped">

      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Booked At</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($bookings as $index => $booking): ?>
          <tr>
            <td><?php echo esc_html($booking['name'] ?? ''); ?></td>
            <td><?php echo esc_html($booking['email'] ?? ''); ?></td>
            <td><?php echo esc_html($booking['time'] ?? ''); ?></td>
            <td>
              <button class="kaem-delete-booking"
                data-index="<?php echo $index; ?>"
                data-post="<?php echo $post->ID; ?>">
                Delete
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
<?php
  }

  // add_meta_boxes
  public function add_meta_boxes()
  {
    add_meta_box(
      'kaem_event_details',
      __('Event Details', 'kaddora-ai-event-manager'),
      [$this, 'render_meta_box'],
      'kaem_event',
      'normal',
      'high'
    );
  }

  // render_meta_box
  public function render_meta_box($post)
  {
    wp_nonce_field('kaem_save_event', 'kaem_nonce');

    $date  = get_post_meta($post->ID, '_kaem_date', true);
    $time  = get_post_meta($post->ID, '_kaem_time', true);
    $venue = get_post_meta($post->ID, '_kaem_venue', true);
    $organizer = get_post_meta($post->ID, '_kaem_organizer', true);

    include KAEM_PLUGIN_DIR . 'templates/admin/event-meta-box.php';
  }

  // save_meta
  public function save_meta($post_id)
  {
    // Security
    if (!isset($_POST['kaem_nonce']) || !wp_verify_nonce($_POST['kaem_nonce'], 'kaem_save_event')) {
      return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!current_user_can('edit_post', $post_id)) return;

    $date = isset($_POST['kaem_date']) ? sanitize_text_field($_POST['kaem_date']) : '';
    $time = isset($_POST['kaem_time']) ? sanitize_text_field($_POST['kaem_time']) : '';
    $venue = isset($_POST['kaem_venue']) ? sanitize_text_field($_POST['kaem_venue']) : '';
    $organizer = isset($_POST['kaem_organizer']) ? sanitize_text_field($_POST['kaem_organizer']) : '';

    if (isset($_POST['kaem_repeat'])) {
      update_post_meta($post_id, '_kaem_repeat', sanitize_text_field($_POST['kaem_repeat']));
    }

    // require
    if (empty($date) || empty($time)) {
      return;
    }

    update_post_meta($post_id, '_kaem_date', $date);
    update_post_meta($post_id, '_kaem_time', $time);
    update_post_meta($post_id, '_kaem_venue', $venue);
    update_post_meta($post_id, '_kaem_organizer', $organizer);
  }
}
