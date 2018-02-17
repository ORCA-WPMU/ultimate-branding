<?php
$url = plugins_url( '', __FILE__ );

return array(
	'logo_and_background' => array(
		'logo_upload' => $url.'/logo.png',
		'logo_upload_meta' => array(
			$url.'/logo.png',
			200,
			200,
			false,
		),
		'fullscreen_bg' => $url.'/background.jpg',
		'fullscreen_bg_meta' => array(
			$url.'/background.jpg',
			2400,
			1596,
			false,
		),
	),
	'form' => array(
		'rounded_nb' => 10,
		'show_remember_me' => 'off',
		'form_bg_transparency' => '75',
		'form_style' => 'flat',
		'form_button_color' => '#228b22',
		'form_button_border' => '0',
		'form_button_rounded' => '0',
		'form_button_text_shadow' => 'off',
		'form_button_shadow' => 'off',
	),
	'form_errors' => array(
		'login_error_link_color' => '#228b22',
		'login_error_link_color_hover' => '#338c33',
		'login_error_transarency' => 75,
	),
	'below_form' => array(
		'show_register_and_lost' => 'off',
		'show_back_to' => 'off',
	),
);
