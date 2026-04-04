<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

delete_option('kaem_enable_ai');
delete_option('kaem_enable_booking');
delete_option('kaem_api_token');

global $wpdb;
$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_kaem_%'");
