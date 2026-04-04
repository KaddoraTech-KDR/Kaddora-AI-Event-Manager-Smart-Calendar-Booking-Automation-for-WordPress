<?php
if (!defined('ABSPATH')) exit;

class KAEM_Event_Renderer
{
  public function render_list($query)
  {
    if (!$query->have_posts()) {
      echo "<p>No events found</p>";
      return;
    }

    echo "<div class='kaem-event-list'>";

    while ($query->have_posts()) {
      $query->the_post();

      $date = get_post_meta(get_the_ID(), '_kaem_date', true);

      echo "<div class='kaem-event-item'>";
      echo "<h3>" . get_the_title() . "</h3>";
      echo "<p>Date: $date</p>";
      echo "</div>";
    }

    echo "</div>";

    wp_reset_postdata();
  }
}
