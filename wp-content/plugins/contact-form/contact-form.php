<?php
/**
 * Plugin Name: Ajax Form sending
 * Plugin URI: http://careers.symphonyno9.agency
 * Description: This is a plugin that allows us to test Ajax functionality in WordPress
 * Version: 1.0.0
 * Author: Vojtěch Klos
 * Author URI: http://careers.symphonyno9.agency
 * License: GPL2
 */

// Add menu page
require_once(__DIR__ . '/admin-page.php');

add_action( 'wp_ajax_contact_form_action', 'process_contact_form' );
add_action( 'wp_ajax_nopriv_contact_form_action', 'process_contact_form' );
function process_contact_form() {
	$errors = [];

	if ( 
		empty( $_POST['nonce'] ) 
		|| ! wp_verify_nonce( $_POST['nonce'], 'custom_action_nonce') 
	) { 
		$errors['nonce'] = 'Nonce is missing.';
	}

	if (empty($_POST['firstname'])) {
		$errors['firstname'] = [
			'error' => 'not set',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_firstname', pll_current_language()) : 'First name is required.'
		];
	}

	if (empty($_POST['lastname'])) {
		$errors['lastname'] = [
			'error' => 'not set',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_lastname', pll_current_language()) : 'Last name is required.'
		];
	}

	if (empty($_POST['phone'])) {
		$errors['phone'] = [
			'error' => 'not set',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_phone', pll_current_language()) : 'Phone is required'
		];
	}

	if (empty($_POST['email'])) {
		$errors['email'] = [
			'error' => 'not set',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_email', pll_current_language()) : 'Email is required.'
		];
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = [
			'error' => 'invalid',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_email_invalid', pll_current_language()) : 'Invalid email.'
		];
	}

	if (empty($_POST['service'])) {
		$errors['service'] = [
			'error' => 'not set',
			'message' => function_exists("get_the_translation") && function_exists("pll_current_language") ? get_the_translation('form_error_service', pll_current_language()) : 'Service is required.'
		];
	}

	if ($_POST['terms'] != 'true') {
		$errors['terms'] = [
			'error' => 'not accepted',
			'message' => 'You have to accept the terms.'
		];
	}

	// check if clinic email is valid
	$clinics = get_posts([
		'post_type' => 'clinic',
		'nopaging' => true
	]);

	// check if clinic email is correct
	global $wpdb;

	if (!empty($_POST['as'])) {
		$errors['as'] = [
			'error' => 'spam',
			'message' => 'Dont be a bot! This field is invisible for people!'
		];
	}

	if (!empty($errors)) {
		wp_send_json_error($errors);
	}



	$form_data = [
		'field' => $_POST['field'],
	];

	// SANITIZE INPUT
	foreach ($form_data as $key => $value) {
		if ($key == 'email') {
			$form_data[$key] = sanitize_email($value);
		} elseif ($key == 'message') {
			$form_data[$key] = sanitize_textarea_field($value);
		} else {
			$form_data[$key] = sanitize_text_field($value);
		}
	}

	// INSERT DATA TO TABLE
	$wpdb->insert('contact_form', $form_data);


	// EMAIL SENDING

	// These are just translations based on polylang (delete if you dont use polylang)
	$email_translations = [
		'field_translation' => get_the_translation('field', pll_current_language()),
	];

	$email_data = array_merge($form_data, $email_translations);

	$bo_template = file_get_contents(__DIR__ . '/backoffice-template.html');
	$confirm_template = file_get_contents(__DIR__ . '/confirmation-template.html');
	
	foreach($email_data as $key => $value) {
		// replace {{key}}
		$bo_template = str_replace('{{'.$key.'}}', $value, $bo_template);
		$confirm_template = str_replace('{{'.$key.'}}', $value, $confirm_template);
		// replace {{ key }}
		$bo_template = str_replace('{{ '.$key.' }}', $value, $bo_template);
		$confirm_template = str_replace('{{ '.$key.' }}', $value, $confirm_template);
	}

	// Confirmation email to user
	$to = $_POST['email'];
	$subject = !empty(get_the_translation('email_client_subject', pll_current_language())) ? get_the_translation('email_client_subject', pll_current_language()) : 'ISCARE | Práve sme dostali vašu správu';
	$body = $confirm_template;
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail( $to, $subject, $body, $headers );

	// Backoffice email
	$to = $_POST['clinic_email'];
	$subject = $_POST['firstname'] . ' ' . $_POST['lastname'] . ' práve vyplnil kontaktný formulár';
	$body = $bo_template;
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail( $to, $subject, $body, $headers );


	$response = [
		'message' => 'Mail sent ok',
		'mail' => $bo_template,
		'confirm_mail' => $confirm_template
	];
	wp_send_json_success($response);
}
?>