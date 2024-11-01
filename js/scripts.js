jQuery(function() {
	jQuery('.ws-form-group button').click(function(){
		var form = jQuery(this).parent('form').data('id');

		jQuery(this).closest('form.ws-form-group').children('.ws-message-response').slideUp();

		var name = jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_name"]').val();
		var email = jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_email"]').val();
		var comment = jQuery('[data-id="' + form + '"]').children('textarea[name="ws_contact_comment"]').val();

		var hasError_name = true;
		if (name.length < 3) {
			jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_name"]').addClass('ws-warning-input');
			hasError_name = true;
		}
		else {
			jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_name"]').removeClass('ws-warning-input');
			hasError_name = false;
		}

		var hasError_email = true;
		if (!ws_validateEmail(email) || email.length < 3) {
			jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_email"]').addClass('ws-warning-input');
			hasError_email = true;
		}
		else {
			jQuery('[data-id="' + form + '"]').children('input[name="ws_contact_email"]').removeClass('ws-warning-input');
			hasError_email = false;
		}

		var hasError_comment = true;
		if (comment.length < 3) {
			jQuery('[data-id="' + form + '"]').children('textarea[name="ws_contact_comment"]').addClass('ws-warning-textarea');
			hasError_comment = true;
		}
		else {
			jQuery('[data-id="' + form + '"]').children('textarea[name="ws_contact_comment"]').removeClass('ws-warning-textarea');
			hasError_comment = false;
		}

		if (hasError_name == false && hasError_email == false && hasError_comment == false)
		{
			jQuery.post('/wp-admin/admin-ajax.php',
				{
					action: 'contacthomepage',
					ws_contact_name: name,
					ws_contact_email: email,
					ws_contact_comment: comment,
					ws_ajax_send_form: 1,
					ws_chack: 'fg' + 'yu'
				},
				function(data){
					if(data == 'OK'){
						jQuery('[data-id="' + form + '"]').children('input, textarea').val('');
						jQuery('[data-id="' + form + '"]').children('.ws-message-successful').slideDown();
					} else {
						jQuery('[data-id="' + form + '"]').children('.ws-message-error').slideDown();
					}
				}
			)
		}
		else
		{
			jQuery('[data-id="' + form + '"]').children('.ws-message-error').slideDown();
		}
		return false;

	});
});

function ws_validateEmail($email) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if ( !emailReg.test($email) ) {
		return false;
	} else {
		return true;
	}
}
