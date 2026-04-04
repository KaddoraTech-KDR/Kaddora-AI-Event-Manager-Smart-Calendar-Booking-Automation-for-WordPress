<?php
if (!defined("ABSPATH")) exit;

class KAEM_Loader
{
  protected $actions = [];
  protected $filters = [];

  // =========================
  // ADD ACTION
  // =========================
  public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->actions[] = [
      'hook' => $hook,
      'component' => $component,
      'callback' => $callback,
      'priority' => $priority,
      'accepted_args' => $accepted_args
    ];
  }

  // =========================
  // ADD FILTER ✅ (IMPORTANT)
  // =========================
  public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
  {
    $this->filters[] = [
      'hook' => $hook,
      'component' => $component,
      'callback' => $callback,
      'priority' => $priority,
      'accepted_args' => $accepted_args
    ];
  }

  // =========================
  // RUN ALL HOOKS
  // =========================
  public function run()
  {
    // Run Actions
    foreach ($this->actions as $action) {
      add_action(
        $action['hook'],
        [$action['component'], $action['callback']],
        $action['priority'],
        $action['accepted_args']
      );
    }

    // Run Filters
    foreach ($this->filters as $filter) {
      add_filter(
        $filter['hook'],
        [$filter['component'], $filter['callback']],
        $filter['priority'],
        $filter['accepted_args']
      );
    }
  }
}
