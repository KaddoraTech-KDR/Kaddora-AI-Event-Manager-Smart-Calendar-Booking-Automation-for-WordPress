<?php
if (!defined('ABSPATH')) exit;

// TOTAL EVENTS
$total_events = wp_count_posts('kaem_event')->publish;

// GET ALL EVENTS
$events = get_posts([
  'post_type'   => 'kaem_event',
  'numberposts' => -1
]);

$total_bookings = 0;
$recent = [];
$monthly = [];

// PROCESS DATA
if (!empty($events)) {

  foreach ($events as $event) {
    $bookings = get_post_meta($event->ID, '_kaem_bookings', true);
    if (!is_array($bookings)) continue;

    $total_bookings += count($bookings);

    foreach ($bookings as $b) {
      $recent[] = $b;

      if (!empty($b['time'])) {
        $month = date('Y-m', strtotime($b['time']));

        if (!isset($monthly[$month])) {
          $monthly[$month] = 0;
        }

        $monthly[$month]++;
      }
    }
  }
}

// SORT MONTHS 
ksort($monthly);

// LATEST 5 BOOKINGS
$recent = array_slice(array_reverse($recent), 0, 5);


// echo '<pre>';
// print_r($monthly);
// echo '</pre>';
?>

<div class="wrap">
  <h1>🚀 Kaddora AI Event Manager</h1>
  <p>Welcome to your AI-powered event dashboard 🚀</p>

  <!-- STATS -->
  <div class="kaem-stats">

    <div class="kaem-card">
      <h2><?php echo esc_html($total_events); ?></h2>
      <p>Total Events</p>
    </div>

    <div class="kaem-card">
      <h2><?php echo esc_html($total_bookings); ?></h2>
      <p>Total Bookings</p>
    </div>

  </div>

  <!-- CHART -->
  <h2>Monthly Bookings</h2>
  <canvas id="kaemChart" height="100"></canvas>

  <!-- recent bookings -->
  <h2>Recent Bookings</h2>

  <table class="widefat">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Time</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($recent)): ?>
        <?php foreach ($recent as $r): ?>
          <tr>
            <td><?php echo esc_html($r['name'] ?? ''); ?></td>
            <td><?php echo esc_html($r['email'] ?? ''); ?></td>
            <td><?php echo esc_html($r['time'] ?? ''); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="3">No bookings found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- js -->
<script>
  var kaemChartData = <?php echo json_encode($monthly); ?>;
</script>