<?php
if (!defined('ABSPATH')) exit;

class KAEM_Elementor
{
  public function init()
  {
    add_action('elementor/widgets/register', [$this, 'register_widget']);
  }

  public function register_widget($widgets_manager)
  {
    if (!did_action('elementor/loaded')) return;

    require_once __DIR__ . '/widgets/class-calendar-widget.php';

    $widgets_manager->register(new KAEM_Elementor_Calendar_Widget());
  }
}
