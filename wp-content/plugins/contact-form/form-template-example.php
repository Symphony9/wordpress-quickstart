<?php
	// This example shows "required" fields for the form to have.
	// It is sent via ajax so the action parameter here is just for you to get with js
	// and use it as url as shown in the script example
?>

<form id="contact-form" action="<?php echo admin_url('admin-ajax.php'); ?>" name="contact-form">
	<input type="hidden" name="action" value="contact_form_action">
	<?php wp_nonce_field( 'custom_action_nonce', 'nonce' ); ?>
</form>

<script>
	// This is how you would send it (an example)
	function submitForm(e) {
		e.preventDefault();
		ga('send', 'event', 'sk-form', 'click', 'send');
		let fields = {};

		for (let i = 0, l = contactForm.elements.length; i < l; i++) {
			let el = contactForm.elements[i];
			let result;
			if (el.id == 'contact-submit') {
				// do nothing
			} else if (el.id == 'terms') {
				fields[el.name] = el.checked;
			} else {
				fields[el.name] = el.value;
			}
		}

		axios({ // using axios library
			method: 'POST',
			url: contactForm.getAttribute('action') + '?action=' + fields.action,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			data: qs.stringify(fields) // this uses qs library to stringify data
		}).then((resp) => {
			if (resp.data.success) {
				// Success!
			} else {
				// Error from backend (could be validation or something)
			}
		}).catch((err) => {
			// Error. Usually is something like 500 HTTP response code
		});
	}
</script>