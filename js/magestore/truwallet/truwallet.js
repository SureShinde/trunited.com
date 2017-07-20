function changeSendStatus(hide, my_email, url) {
    var email = $('truwallet_email_input').value;
    var value = $('truwallet_value_input').value;
    var message = $('truwallet_message_textarea').value;
    if (CustomercreditFormContent.validator.validate() && checkValue(hide, value) && email != my_email) {
        $('truwallet_show_loading_p').show();
        $('truwallet_send_credit_button').hide();
        $('truwallet_cancel_button').hide();        
        new Ajax.Request(url, {
            method: 'post',
            postBody: '',
            parameters: {'email':email, 'value': value, 'message': message},
            onComplete: function(response) {
                if (response.responseText.isJSON()) {
					if (typeof(reloadAllBlock) != 'undefined') reloadAllBlock();
                    var res = response.responseText.evalJSON();
                    if (res.success == 1) {
                        $('truwallet_show_loading_p').hide();
                        $('truwallet-form-content').submit();
                    }
                }
            }
        });
    }
}

function checkEmailExisted(my_email, url) {
    $('advice-your-email').hide();
    //$('truwallet_send_credit_button').type = 'button';
    var email = $('truwallet_email_input').value;
    validate('truwallet_email_input');
    if (validateEmail(email)) {
        $('truwallet_show_alert').hide();
        $('truwallet_show_success').hide();
        if (my_email != email) {
            $('truwallet_show_loading').show();
            url += "checkemail/email/" + email;
            new Ajax.Request(url, {
                method: 'post',
                postBody: '',
                onComplete: function(response) {
                    $('truwallet_show_loading').hide();
                    if (response.responseText.isJSON()) {
						if (typeof(reloadAllBlock) != 'undefined') reloadAllBlock();
                        var res = response.responseText.evalJSON();
                        if (res.existed == 1) {
                            $('truwallet_show_success').show();
                        }
                        else {
                            $('truwallet_show_alert').show();
                        }
                        //$('truwallet_send_credit_button').type = 'submit';
                    }
                }
            });
        }
        else {
            $('advice-your-email').show();
            inValidate('truwallet_email_input');
        }
    }

}

function checkValue(hid, val) {
    if (val - hid > 0 && val != null) {
        $('advice-validate-max-number').show();
        inValidate('truwallet_value_input');
        return false;
    }
    else {
        $('advice-validate-max-number').hide();
        return true;
    }
}
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function isNumeric(input) {
    var re = /^-{0,1}\d*\.{0,1}\d+$/;
    return (re.test(input));
}

function inValidate(element) {
    $(element).setStyle({
        border: '1px dashed #eb340a',
        background: '#faebe7'
    });
}
function validate(element) {
    $(element).setStyle({
        border: '1px solid #b6b6b6',
        background: '#fff'
    });
}

function setButton(el) {
    if (el.checked) {
        $('is_check_send_email').value = 'yes';
    }
}
function enableCheckbox() {
    if ($('truwalletcheck'))
        $('truwalletcheck').removeAttribute('disabled');
    if ($('checkout_cc_inputtext'))
        $('checkout_cc_inputtext').removeAttribute('disabled');
    if ($('checkout-cc-button'))
        $('checkout-cc-button').removeAttribute('disabled');
    if ($('checkout-cc-button-cancel'))
        $('checkout-cc-button-cancel').removeAttribute('disabled');
}
function showEditText(el) {
    $('checkout-cc-input').hide();
    $('checkout-cc-img').hide();
    $('checkout_cc_input_alert').show();
    $('checkout-cc-button').show();
    $('checkout-cc-button-cancel').show();
}

function changeUseCustomercredit(el, url) {
    if (el.checked) {
        $('cc_checkout').show();
        $('checkout_cc_inputtext').value = '0';
    } else {
        $('cc_checkout').hide();
        new Ajax.Request(url, {
            method: 'post',
            parameters: {check_credit: 'unchecked'},
            onComplete: function(response) {
                if (response.responseText.isJSON()) {
					if (typeof(reloadAllBlock) != 'undefined') reloadAllBlock();
                    var res = response.responseText.evalJSON();
                    if (res.isonestep) {
                        var shipping_method_url = res.isonestep;
                        save_shipping_method(shipping_method_url, 1, 1);
                    }
                }
            }
        });
    }

}
function validateCheckOutCC(current_amount) {
    $('checkout-cc-button').type = 'submit';
    $('advice-validate-number-checkout_cc_smaller').hide();
    validate('checkout_cc_inputtext');
    var amount = $('checkout_cc_inputtext').value;
    if (isNumeric(amount) && amount - 0 >= 0 && amount - current_amount > 0) {
        $('advice-validate-number-checkout_cc_smaller').show();
        inValidate('checkout_cc_inputtext');
        $('checkout-cc-button').type = 'button';
    }
}
function updateCustomerCredit(url, current_amount, state) {

    var amount = $('checkout_cc_inputtext').value;
    if (current_amount == 0) {
        amount = 0;
    }

    if (isNumeric(amount) && amount !== "" && (amount - 0 >= 0) && (amount - current_amount <= 0)) {
        $('loading-credit').show();
        $('truwallet_cc_success_img').hide();
        new Ajax.Request(url, {
            method: 'post',
            postBody: '',
            parameters: {'credit_amount':amount, 'state': state},
            onComplete: function(response) {
                if (response.responseText.isJSON()) {
					if (typeof(reloadAllBlock) != 'undefined') reloadAllBlock();
                    var res = response.responseText.evalJSON();
                    $('checkout_cc_inputtext').setStyle({
                        backgroundColor: 'rgb(253, 246, 228)'
                    });
                    $('loading-credit').hide();
                    $('truwallet_cc_success_img').show();
                    $('checkout-cc-input').value = res.amount;
                    amount = res.amount;
                    if (res.saveshippingurl) {
                        var shipping_method_url = res.saveshippingurl;
                        save_shipping_method(shipping_method_url, 1, 1);
                    } else if (res.payment_html) {
                        //reload payment
                        $('checkout-payment-method-load').update(res.payment_html);
                    }
                }
            }
        });
    }

}