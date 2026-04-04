<?php
if (!defined('ABSPATH')) exit;

class KAEM_Event_Filters
{
  public function filter_by_date($query, $date)
  {
    $query->set('meta_query', [
      [
        'key' => '_kaem_date',
        'value' => $date,
        'compare' => '='
      ]
    ]);

    return $query;
  }
}
