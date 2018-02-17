<?php
$url = plugins_url( '', __FILE__ );

return array(
	'logo_and_background' => array(
		'fullscreen_bg' => $url.'/background.jpg',
		'fullscreen_bg_meta' => array(
			$url.'/background.jpg',
			2400,
			1596,
			false,
		),
	),
	'form' => array(
		'form_bg_color' => '#eeffee',
		'form_bg_transparency' => 75,
		'form_button_color' => '#119911',
		'rounded_nb' => 20,
	),
	'below_form' => array(
		'show_back_to' => 'off',
	),
);
