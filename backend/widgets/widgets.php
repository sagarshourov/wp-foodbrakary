<?php

/**
 * File Type: Widgets
 */
function register_widget_areas() {
	register_sidebar( array(
		'name'          => __( 'Restaurents Element Sidebar', 'foodbakery' ),
		'id'            => 'restaurents-element-right-sidebar',
		'description'   => __( 'Add widgets here to appear in your restaurents element right sidebar.', 'foodbakery' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );
}
add_action( 'widgets_init', 'register_widget_areas', 12 );
