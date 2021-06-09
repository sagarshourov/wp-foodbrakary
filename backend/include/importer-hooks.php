<?php

add_action( 'foodbakery_import_users', 'foodbakery_import_users_handle' );
if ( ! function_exists( 'foodbakery_import_users_handle' ) ) {
	function foodbakery_import_users_handle( $obj ) {
		if (class_exists('foodbakery_user_import')) {
			ob_start();
			$foodbakery_user_import = new foodbakery_user_import();
            $foodbakery_user_import->foodbakery_import_user_demodata( false, false, false, $obj->users_data_path );
			ob_end_clean();
			$obj->action_return = true;
		} else {
			$obj->action_return = false;
		}
	}
}

add_action( 'foodbakery_import_plugin_options', 'foodbakery_import_plugin_options_handle' );
if ( ! function_exists( 'foodbakery_import_plugin_options_handle' ) ) {
	function foodbakery_import_plugin_options_handle( $obj ) {
		if ( function_exists( 'foodbakery_demo_plugin_data' ) ) {
			foodbakery_demo_plugin_data( $obj->plugins_data_path );
			$obj->action_return = true;
		} else {
			$obj->action_return = false;
		}
	}
}