<?php
if (!defined('ABSPATH')) exit;

class KAEM_Post_Types
{
  public function init($loader)
  {
    // CPT
    $loader->add_action('init', $this, 'register_post_type');

    // Columns
    $loader->add_action('manage_kaem_event_posts_columns', $this, 'add_columns');

    $loader->add_action('manage_kaem_event_posts_custom_column', $this, 'render_columns', 10, 2);

    $loader->add_filter('manage_edit-kaem_event_sortable_columns', $this, 'sortable_columns');

    $loader->add_action('pre_get_posts', $this, "sort_by_meta");

    $loader->add_action('restrict_manage_posts', $this, 'add_filters');

    $loader->add_action('pre_get_posts', $this, 'filter_query');
  }

  // filter_query
  public function filter_query($query)
  {
    if (!is_admin() || !$query->is_main_query()) return;

    if ($query->get('post_type') !== 'kaem_event') return;

    if (!empty($_GET['kaem_filter_date'])) {
      $query->set('meta_query', [
        [
          'key' => '_kaem_date',
          'value' => sanitize_text_field($_GET['kaem_filter_date']),
          'compare' => '='
        ]
      ]);
    }
  }

  // add_filters
  public function add_filters()
  {
    global $typenow;

    if ($typenow !== 'kaem_event') return;

    $selected = $_GET['kaem_filter_date'] ?? '';
?>
    <input type="date" name="kaem_filter_date" value="<?php echo esc_attr($selected); ?>" />
<?php
  }

  // sort_by_meta
  public function sort_by_meta($query)
  {
    if (!is_admin() || !$query->is_main_query()) return;

    if ($query->get('post_type') !== 'kaem_event') return;

    $orderby = $query->get('orderby');

    if ($orderby == 'kaem_date') {
      $query->set('meta_key', '_kaem_date');
      $query->set('orderby', 'meta_value');
    }

    if ($orderby == 'kaem_time') {
      $query->set('meta_key', '_kaem_time');
      $query->set('orderby', 'meta_value');
    }
  }

  // sortable_columns
  public function sortable_columns($columns)
  {
    $columns['kaem_date'] = 'kaem_date';
    $columns['kaem_time'] = 'kaem_time';

    return $columns;
  }

  // register_post_type
  public function register_post_type()
  {
    register_post_type('kaem_event', [
      'labels' => [
        'name' => __('Events', 'kaddora-ai-event-manager'),
        'singular_name' => __('Event', 'kaddora-ai-event-manager'),
      ],
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-calendar',
      'supports' => ['title', 'editor'],
      'show_in_rest' => true
    ]);
  }

  // add_columns
  public function add_columns($columns)
  {
    $columns['kaem_date'] = 'Date';
    $columns['kaem_time'] = 'Time';
    $columns['kaem_venue'] = 'Venue';
    return $columns;
  }

  // render_columns
  public function render_columns($column, $post_id)
  {
    if ($column === 'kaem_date') {
      echo esc_html(get_post_meta($post_id, '_kaem_date', true));
    }

    if ($column === 'kaem_time') {
      echo esc_html(get_post_meta($post_id, '_kaem_time', true));
    }

    if ($column === 'kaem_venue') {
      echo esc_html(get_post_meta($post_id, '_kaem_venue', true));
    }
  }
}
