(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 * 
	 */

// ==============================================
// VARIABLES COLORS AND STYLE DINAMIC
// ===============================================

let ColorUnselectCard = 'rgb(108, 117, 125)'; //unselect item
let ColorSelectCard = '#007bff'; //selec

// ---------------------
// Functions
// ---------------------

// Get Data For Form
/*
	Ajax Auxiliary Functions
*/
let $noticeMessage = `<div class='ResponseMessage notice'>
						<div class='message_rectangle notice_color'></div>
						<p class='message_icon notice_color'><i class="fa-solid fa-clock"></i></i></p>
						<button class='exit-message' type='button' aria-label='Dismiss'><i class='fa-solid fa-xmark'></i></button>
						<div class='message-paragraph'>
							<b class='bold-message'>Loading...</b><br>
							<span class='text-message'>Processing request...</span>
						</div>
					</div>`; 

function buildRequestPayload($form, action) {
	const payload = $form.serializeArray();
	payload.push({ name: 'action', value: action });
	payload.push({ name: 'nonce', value: TFPayment.nonce });
	payload.push({ name: 'lang', value: TFContent.language });
	payload.push({ name: 'language', value: TFContent.language });
	return payload;
}

function extractResponseHtml(payload) {
	if (!payload) return null;

	if (payload.data && payload.data.html) {
		return payload.data.html;
	}

	if (payload.html) {
		return payload.html;
	}

	return null;
}

function processAjaxResponse(payload, $response) {
	const html = extractResponseHtml(payload);
	if (html && $response) {
		$response.html(html);
	}

	return {
		payload,
		html,
		success: !!(payload && payload.success),
		errors: payload && payload.errors ? payload.errors : null
	};
}

function handleAjaxFailure($response, xhr, textStatus, errorThrown, logPrefix) {
	let result = null;

	if (xhr.responseJSON) {
		result = processAjaxResponse(xhr.responseJSON, $response);
		if (result.success) {
			console.warn(`${logPrefix}: request completed but jQuery reported ${textStatus || 'parsererror'}.`, { xhr });
			return;
		}
	} else if (typeof xhr.responseText === 'string' && xhr.responseText.trim().length > 0) {
		try {
			const parsed = JSON.parse(xhr.responseText);
			result = processAjaxResponse(parsed, $response);
			if (result.success) {
				console.warn(`${logPrefix}: manual JSON parse succeeded despite failure callback.`, { xhr });
				return;
			}
		} catch (err) {
			if (textStatus === 'parsererror') {
				console.error('Failed to parse JSON response', err);
			}
		}
	}

	if (!result || !result.html) {
		const fallbackMessage = "<div class='ResponseMessage error'>" +
			"<div class='message_rectangle error_color'></div>" +
			"<p class='message_icon error_color error'>!</p>" +
			"<button class='exit-message' type='button' aria-label='Dismiss'>A-</button>" +
			"<div class='message-paragraph'>" +
				"<b class='bold-message'>Error</b><br>" +
				"<span class='text-message error'>An unexpected error occurred. Please try again.</span>" +
			"</div>" +
		"</div>";

		$response.html(fallbackMessage);
	}

	console.error(`${logPrefix} (status ${xhr.status || 'unknown'}, textStatus: ${textStatus || 'n/a'})`, errorThrown || '', xhr);
}

// Clear Inputs after ajax payment
function clearInputs(form) {

	$(form).find('input, textarea').val('');

	$(form).find('select').each(function () {
		if ($(this).find('option[value=""]').length) {
			$(this).val('').trigger('change'); 
		}
	});

	$('#Country').val('US').trigger('change');
	$('#PhoneType').val(1).trigger('change');

}

// Make Payment Function (Server interaction > make payment)
function makePayment() {
	const $submit = $("#trf-payment-form button[type='submit']");
	const $form = $('#trf-payment-form');
	
	const $response = $("#qp-embedded-container"); 
	const $responseSuccess = $('#qp-embedded-container');
		$response
	.removeClass("success error")            
	.html($noticeMessage);

	$submit.prop("disabled", true);
	$submit.find('i').addClass('spinner-border');

	const payload = buildRequestPayload($form, 'trufuel_payment');

	$.ajax({
		url: TFPayment.ajaxUrl,
		method: 'POST',
		data: payload,
		dataType: 'json'
	}).done(function (resp) {
		const result = resp;

		if (result.success) {
			$responseSuccess.html(extractResponseHtml(result));
			sendEmail(result.data.transactionID); // send Email just when the information has been entered correctly
			clearInputs($("#trf-payment-form"));
		} else if (result.errors) {
			console.log(result.errors);
		}else{
			$response.html(extractResponseHtml(result));
		}
	}).fail(function (xhr, textStatus, errorThrown) {
		handleAjaxFailure($response, xhr, textStatus, errorThrown, 'Error AJAX making payment');
	}).always(function() {
		$submit.prop('disabled', false);
	});
}

// Send Email Confirm
function sendEmail(transactionID) {
	const $submit = $("#trf-payment-form button[type='submit']");
	const $form   = $('#trf-payment-form');
	const $transactionInput = $('#transaction-id');

	$transactionInput.val(transactionID);
	
	const $response = $("#email-message"); 
		$response
	.removeClass("success error")            
	.html('');

	$submit.prop("disabled", true);

	const payload = buildRequestPayload($form, 'trufuel_send_email');

	$.ajax({
		url: TFPayment.ajaxUrl,
		method: 'POST',
		data: payload,
		dataType: 'json'
	}).done(function (resp) {
		const result = processAjaxResponse(resp);
		console.log(result.html);

		if (!result.success && result.errors) {
			console.log(result.errors);
		}
	}).fail(function (xhr, textStatus, errorThrown) {
		handleAjaxFailure($response, xhr, textStatus, errorThrown, 'Error AJAX send email data');
	}).always(function() {
		$submit.prop('disabled', false);
	});
}

// CheckCaptha Function (create new token reCaptcha)
function checkCaptcha() {
	const siteKey = TFPayment.recaptchaSiteKey;
	if (!siteKey) {
		console.warn('No reCAPTCHA site key configured');
		return Promise.reject(new Error('Missing site key'));
	}

	return new Promise((resolve, reject) => {
		grecaptcha.ready(() => {
		grecaptcha.execute(siteKey, { action: 'submit' })
			.then(token => {
			document.getElementById('token').value = token;
			document.getElementById('submit-btn-payment').disabled = false;
			resolve(token);
			})
			.catch(reject);
		});
	});
}

/**
 * REQUIRED INPUTS CHECK FUNCTION
 */

// find inputs inside form
const input	= document.querySelectorAll('input');

// Function to check that all required fields have been filled in
function CheckRequired(){

	let isValid = true; // valid marker

	input.forEach(function(ctrl) {
		// variable declaration
		let formGroup = ctrl.closest('.form-group'); //find all form-groups in the document
		const required = ctrl.hasAttribute('required'); //find out if the selected input is a required field
		const value = ctrl.value; //input value

		// when the input has the `required` attribute
		if(required) {

			let invalidFeedback = formGroup.querySelector('.invalid-feedback');//We look for the closest invalid-feedback class

			if (value.trim().length == 0) {
				ctrl.classList.add('empty');
				invalidFeedback.innerHTML = 'This Field is required';
				isValid = false;
			}
			else{
				ctrl.classList.remove('empty');
			    invalidFeedback.innerHTML = '';
			}
		}

	});

	return isValid;
}

// Function to know if an input is full or not
function EmptyInput(input){

	 //Declaration of variables
	 let formGroup = input.closest('.form-group');  //find all form-groups in the document
     const required = input.hasAttribute('required'); //find out if the selected input is a required field
  	 let value = input.value; //input value

  	 //Required fiels
  	 if (required) {
  	 	let invalidFeedback = formGroup.querySelector('.invalid-feedback');

  	 	//when the required input has value inside
  	 	if (value.trim().length > 0) {
	  	 	input.classList.remove('empty');
		    invalidFeedback.innerHTML = '';
  	 	}
  	 }
}

/**
 *  VALIDATE INPUTS FORMAT
 */

//FUNCTION TO VALIDATE FIELDS IN THE FORM
const validateCardNumber = document.getElementById('CardNumberValidate');

// CARD NUMBER
function ValidateNumberCard(CardNumber){

    const iconsCard = document.querySelectorAll('.mx-1');

    iconsCard.forEach(function(element) {
      element.style.color = ColorUnselectCard;
    });
    
    if (CardNumber.value[0] === '4'){// Visa Card
        // validateCardNumber.innerHTML = 'This is a Visa Card';
        
        validateCardNumber.innerHTML = "";
        formatComunCard(CardNumber); //format for visa card

        //put the select color for this icon
        let visaIcon  = document.querySelectorAll('.fa-cc-visa');
        visaIcon.forEach(function (icon) {icon.style.color = ColorSelectCard});

    }else if (CardNumber.value[0] === '5') {// Master Card
      // validateCardNumber.innerHTML = 'This is a Master Card Card';
        validateCardNumber.innerHTML = "";
        formatComunCard(CardNumber);

        
        //put the select color for this icon
        let mastercardIcon  = document.querySelectorAll('.fa-cc-mastercard');
        mastercardIcon.forEach(function (icon) {icon.style.color = ColorSelectCard});


    }else if(CardNumber.value[0] === '3'){//American Express

      // validateCardNumber.innerHTML = 'This is an American Express Card';
     
      validateCardNumber.innerHTML = "";
      formatAmericanCard(CardNumber);

      let amexIcon  = document.querySelectorAll('.fa-cc-amex');
      amexIcon.forEach(function (icon) {icon.style.color = ColorSelectCard});

    }else if(CardNumber.value[0] === '6'){//Discover card
      
      //  validateCardNumber.innerHTML = 'This is a Discover Card';
   
       validateCardNumber.innerHTML = "";
       formatComunCard(CardNumber);
       
       let discoverIcon  = document.querySelectorAll('.fa-cc-discover');
       discoverIcon.forEach(function (icon) {icon.style.color = ColorSelectCard});

    }else if(CardNumber.value.trim().length !== 0){
      formatComunCard(CardNumber);
      // sendButton.disabled = true;
      validateCardNumber.innerHTML = "The card number is not valid";
      iconsCard.forEach(function(element) {element.style.color = ColorUnselectCard});
    }else{
      validateCardNumber.innerHTML = "";
    }
};

// CVV
function ValidateCVVCard(fieldCVV) {
  const Validate = document.getElementById('ValidateCVV');
  const submit = document.getElementById('submit-btn-payment');
  let CardNumber = document.getElementById('CardNumber');
  let CVV_length = document.getElementById('CVV').value.length;
  // let fieldCVV = document.getElementById('CVV');
  let fieldAddress = document.getElementById('address');
  const regex = /^[0-9]*$/;
  const Onlynumbers = regex.test(fieldCVV.value);
  console.log(Onlynumbers);
  if (fieldCVV.value !== '' && !Onlynumbers){

    ValidateCVV.innerHTML = 'This CVV is invalid';
    submit.disabled = true;

  }else if (CardNumber.value[0] === '4' || CardNumber.value[0] === '5' || CardNumber.value[0] === '6'){// Visa Card

      ValidateCVV.innerHTML = '';
      submit.disabled = false;
      moveCursor(fieldCVV, fieldAddress, 3);
      
  }else if (CardNumber.value[0] === '3') {// Master Card
    if(CVV_length === 4) {
      ValidateCVV.innerHTML = '';
      submit.disabled = false;
      moveCursor(fieldCVV, fieldAddress, 4);
    }

  }else{
    ValidateCVV.innerHTML = '';
    submit.disabled = false;
  }
};

// DATE
function ValidateDate(month) {
  const Validate = document.getElementById('ExpirationValidate');
  const submit = document.getElementById('submit-btn-payment');

  if(month !== '' && month < 1 || month > 12){
     Validate.innerHTML = 'This Date Expiration is invalid';
     submit.disabled = true;
  }
  else{
   Validate.innerHTML = '';
   submit.disabled = false;
  }

 }
 
 // YEAR
 function ValidateYear(year) {
   const Validate = document.getElementById('ExpirationValidate');
   const submit = document.getElementById('submit-btn-payment');

   var today = new Date();
   var yearNow = today.getFullYear(); 
   var yeardigit = yearNow%100;

   if(year !== '' && yeardigit > year){
     Validate.innerHTML = 'This Date is less than the current date';
     submit.disabled = true;
   }
   else{
     Validate.innerHTML = '';
     submit.disabled = false;
   }

 }

 // ADDRESS
 function validateAddress(Address){
   const Validate = document.getElementById('ValidateAddress');
   const submit = document.getElementById('submit-btn-payment');

   const regex = /^[0-9]*$/;
   const Onlynumbers = regex.test(Address);

   if (Address !== '' && Onlynumbers){
     Validate.innerHTML = 'This Address is invalid';
     submit.disabled = true;
   }
   else{
     Validate.innerHTML = '';
     submit.disabled = false;
   }

 }
 
 // ZIP CODE
 function validateZipCode(ZipCode){
   const Validate = document.getElementById('ValidateZipCode');
   const submit = document.getElementById('submit-btn-payment');
   // const Address = document.getElementById('address').value;
   const regex = /^[A-Za-z]+$/;
   const letters = regex.test(ZipCode);
   if (ZipCode !== '' && letters){
     Validate.innerHTML = 'This Zip Code is invalid';
     submit.disabled = true;
   }else{
     Validate.innerHTML = '';
     submit.disabled = false;
   }
 }
 
 // AMOUNT (has been float)
 function validateAmt(Amt) {
  const Validate = document.getElementById('ValidateAmount');
  const submit = document.getElementById('submit-btn-payment');

  const regex = /^\d*\.\d+$/;
  const float = regex.test(Amt);
  
  if (Amt !== '' && !float) {
    Validate.innerHTML = 'Amount must be a valid decimal number (e.g. 3.14)';
    submit.disabled = true;
  }
  else{
    Validate.innerHTML = '';
    submit.disabled = false;
  }

}

// MOVE CURSOR (move the cursor according to the maximum length limit of the input)
function moveCursor(start, end, limit) {

	if (start.value.length === limit) {
		end.focus();
	}

}

// FORMAT FOR CARD NUMBER
function formatComunCard(input) {
	// Remove any non-numeric characters
	let value_input = input.value.replace(/\D/g, '');
	
	// Apply the format of 4 groups of 4 digits with space
	value_input = value_input.replace(/(\d{4})(?=\d)/g, '$1 ');

	// Assign the formatted value to the input field
	input.value = value_input;

	// Length up to 19 characters
	if (input.value.length > 19) {
		input.value = input.value.substring(0, 19);
	}
}


function formatAmericanCard(input) {
	let value = input.value.replace(/\D/g, ''); // Eliminar cualquier carácter que no sea un número
		
	if (value.length <= 4) {
		input.value = value; // Si tiene 4 o menos dígitos, no agregamos espacio
	} 
	else if (value.length <= 10) {
		input.value = value.replace(/(\d{4})(\d{1,6})/, '$1 $2'); // Después de 4 dígitos, agregamos un espacio
	} 
	else {
		input.value = value.replace(/(\d{4})(\d{1,6})(\d{1,5})/, '$1 $2 $3'); // Después de 10 dígitos, agregamos el segundo espacio
	}

	// Length up to 19 characters
	if (input.value.length > 17) {
		input.value = input.value.substring(0, 17);
	}

	}

	function formatCVV(input) {
	// Remove any non-numeric characters
	let value_input = input.value.replace(/\D/g, '');

	// Assign the formatted value to the input field
	input.value = value_input;

	// Length up to 19 characters
	if (input.value.length > 4) {
		input.value = input.value.substring(0, 4);
	}
}

// ---------------------
// EVENTS
// ---------------------

$(document).on('click', '.exit-message', function(event){
	event.preventDefault();
	let response = $('.ResponseMessage');
	$(this).closest(response).remove();
});

//----------------------
// Jquery Document Ready
//----------------------

$(document).ready(function() {
	// 3) Execute Captcha
	checkCaptcha();
});

// Event Listener individually for each input
input.forEach(ctrl => {
 
  ctrl.addEventListener('input', e => {
      if (ctrl.id === 'CardNumber'){
        ValidateNumberCard(ctrl);
      }
      if(ctrl.id === 'CVV'){
        ValidateCVVCard(ctrl);
      }
      if(ctrl.id === 'monthCard'){
        ValidateDate(ctrl.value);
      }
      if(ctrl.id === 'yearCard'){
        ValidateYear(ctrl.value);
      }
      if(ctrl.id === 'address'){
        validateAddress(ctrl.value);
      }
      if(ctrl.id === 'ZipCode'){
        validateZipCode(ctrl.value);
      }
      if(ctrl.id === 'amount'){
        validateAmt(ctrl.value);
      }
  		 EmptyInput(ctrl);
  });
});

/**
 * SUBMIT FORM
 */
$(document).on('click', '#submit-btn-payment', async function(event) {
	//prevent default behavior
	event.preventDefault();

	if (CheckRequired()) {
		try {
			console.log('before captcha');
			const token = await checkCaptcha();
			console.log('captcha token:', token);
			if (token) makePayment();
		} catch (err) {
			console.error('Captcha failed', err);
			$(this).prop('disabled', false);
		}
	}
});

$(document).on('input', '#monthCard', function(){
	var month = document.getElementById('monthCard');
	var year =  document.getElementById('yearCard');
	moveCursor(month,year,2);
});

$(document).on('input', '#yearCard', function(){
	var year = document.getElementById('yearCard');
	var cvv =  document.getElementById('CVV');
	moveCursor(year, cvv,2);
});

})( jQuery );

