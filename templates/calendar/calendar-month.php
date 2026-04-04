<?php
if (!defined('ABSPATH')) exit;

$month  = isset($data['month']) ? intval($data['month']) : date('m');
$year   = isset($data['year']) ? intval($data['year']) : date('Y');
$events = isset($data['events']) ? $data['events'] : [];

$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
?>

<div class="kaem-calendar">

  <div class="kaem-nav">
    <button class="kaem-prev"
      data-month="<?php echo esc_attr($month); ?>"
      data-year="<?php echo esc_attr($year); ?>">
      <span>←</span>
    </button>

    <span class="kaem-title">
      <?php echo esc_html(date('F Y', strtotime("$year-$month-01"))); ?>
    </span>

    <button class="kaem-next"
      data-month="<?php echo esc_attr($month); ?>"
      data-year="<?php echo esc_attr($year); ?>">
      <span>→</span>
    </button>
  </div>

  <div class="kaem-grid">

    <?php for ($day = 1; $day <= $days_in_month; $day++):
      $date = $year . '-' .
        str_pad($month, 2, '0', STR_PAD_LEFT) . '-' .
        str_pad($day, 2, '0', STR_PAD_LEFT);
    ?>

      <div class="kaem-day">
        <div class="kaem-day-header">
          <span class="kaem-day-number">
            <?php echo esc_html($day); ?>
          </span>
        </div>

        <div class="kaem-day-body">
          <?php if (!empty($events[$date])): ?>
            <ul class="kaem-event-list">
              <?php foreach ($events[$date] as $event): ?>
                <li>
                  <span class="kaem-event"
                    data-id="<?php echo esc_attr($event['id']); ?>">
                    <?php echo esc_html($event['title']); ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <div class="kaem-no-event"></div>
          <?php endif; ?>
        </div>

      </div>

    <?php endfor; ?>

  </div>

  <?php if (empty($events)) : ?>
    <p class="kaem-empty">No events found</p>
  <?php endif; ?>

  <div id="kaem-modal" class="kaem-modal">
    <div class="kaem-modal-overlay"></div>

    <div class="kaem-modal-content">
      <span class="kaem-close">&times;</span>
      <div id="kaem-modal-body"></div>
    </div>
  </div>

</div>