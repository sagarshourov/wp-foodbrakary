<?php
global $foodbakery_settings_init;

require_once ABSPATH . '/wp-admin/includes/file.php';

// Home Demo
$foodbakery_demo = foodbakery_get_settings_demo('demo.json');

$foodbakery_settings_init = array(
	"plugin_options" => $foodbakery_demo,
);