<?php
if (!defined('ABSPATH')) exit;
?>

<div class="kaem-meta-box">

  <div class="kaem-row">
    <div class="kaem-field">
      <label>Date</label>
      <input type="date" name="kaem_date" value="<?php echo esc_attr($date); ?>">
    </div>

    <div class="kaem-field">
      <label>Time</label>
      <input type="time" name="kaem_time" value="<?php echo esc_attr($time); ?>">
    </div>
  </div>

  <div class="kaem-field">
    <label>Venue</label>
    <input type="text" name="kaem_venue" value="<?php echo esc_attr($venue); ?>">
  </div>

  <div class="kaem-field">
    <label>Organizer</label>
    <input type="text" name="kaem_organizer" value="<?php echo esc_attr($organizer); ?>">
  </div>

  <div class="kaem-field">
    <label>Repeat</label>
    <select name="kaem_repeat">
      <option value="">None</option>
      <option value="daily">Daily</option>
      <option value="weekly">Weekly</option>
      <option value="monthly">Monthly</option>
    </select>
  </div>

</div>