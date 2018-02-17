<?php
$url = plugins_url( '', __FILE__ );

return array(
	'logo_and_background' => array(
		'fullscreen_bg' => $url.'/background.jpg',
		'fullscreen_bg_meta' => array(
			$url.'/background.jpg',
			2400,
			1600,
			false,
		),
	),
	'form' => array(
		'rounded_nb' => 0,
		'form_style' => 'flat',
		'form_button_color' => '#000000',
		'form_button_border' => '0',
		'form_button_rounded' => '0',
		'form_button_text_shadow' => 'off',
		'form_button_shadow' => 'off',
	),
	'below_form' => array(
		'register_and_lost_color_link' => '#ffffff',
		'back_to_color_link' => '#ffffff',
	),
);
