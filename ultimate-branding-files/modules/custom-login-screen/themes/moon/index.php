<?php
$url = plugins_url( '', __FILE__ );

return array(
	'logo_and_background' => array(
		'show_logo' => 'off',
		'fullscreen_bg' => $url.'/background.jpg',
		'fullscreen_bg_meta' => array(
			$url.'/background.jpg',
			2400,
			1596,
			false,
		),
	),
	'below_form' => array(
		'back_to_color_link' => '#aaa',
		'back_to_color_hover' => '#ddd',
		'register_and_lost_color_link' => '#aaa',
		'register_and_lost_color_hover' => '#ddd',
	),
);
