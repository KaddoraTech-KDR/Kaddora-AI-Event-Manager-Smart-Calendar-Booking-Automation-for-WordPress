<?php
if (!defined('ABSPATH')) exit;

class KAEM_Elementor_Calendar_Widget extends \Elementor\Widget_Base
{
  public function get_name()
  {
    return 'kaem_calendar';
  }

  public function get_title()
  {
    return 'Event Calendar';
  }

  public function get_icon()
  {
    return 'eicon-calendar';
  }

  public function get_categories()
  {
    return ['general'];
  }

  protected function render()
  {
    echo do_shortcode('[kaem_calendar]');
  }
}
