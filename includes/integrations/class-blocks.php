<?php
if (!defined('ABSPATH')) exit;

class KAEM_Blocks
{
  public function init()
  {
    add_action('init', [$this, 'register_block']);
  }

  public function register_block()
  {
    register_block_type(
      'kaem/calendar',
      [
        'render_callback' => [$this, 'render']
      ]
    );
  }

  public function render()
  {
    return do_shortcode('[kaem_calendar]');
  }
}
