<?php
$events = get_posts([
  'post_type' => 'kaem_event',
  'numberposts' => -1
]);
?>

<div class="wrap kaem-events">
  <h1 class="kaem-title">📅 Events</h1>

  <div class="kaem-events-card">

    <table class="widefat fixed striped kaem-events-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Date</th>
          <th>Venue</th>
        </tr>
      </thead>

      <tbody>
        <?php if (!empty($events)) : ?>
          <?php foreach ($events as $event) :
            $date = get_post_meta($event->ID, '_kaem_date', true);
            $venue = get_post_meta($event->ID, '_kaem_venue', true);
          ?>
            <tr>
              <td class="kaem-event-title">
                <?php echo esc_html($event->post_title); ?>
              </td>
              <td><?php echo esc_html($date); ?></td>
              <td><?php echo esc_html($venue); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="3" class="kaem-empty">No events found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

  </div>
</div>