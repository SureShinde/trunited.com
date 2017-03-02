alert('aaaa');

/*
$j = jQuery.noConflict();

if ($j('.wrm').val() == '') {
    $j('.close-icon').css({display: "none"});
}

$j(".wrm").on("input", function (e) {
    var value = $j(this).val();
    if ($j(this).data("lastval") != value) {
        $j(this).data("lastval", value);
    }

    if (value == '') {
        $j('.close-icon').css({display: "none"});
    } else {
        $j('.close-icon').css({display: "inline-block"});
    }
});

function closeX()
{
    $j('.close-icon').hide();
    $('affiliate_name').value = '';
    $('affiliate_id').value = '';
    $j('#refers_mobile_error').hide();
    $j('#refers_error').hide();
    $j('#affiliate_message_hidden_email').hide();
}

$j('.close-icon').click(function () {
    closeX();
});

function hideClose(el){
    closeX();
}

$j('#form-validate').submit(function () {
    var affiliate_name = $('affiliate_name').value;
    var no_refers_me = $('no_refers_me').checked;
    if (affiliate_name == '' && no_refers_me === false) {
        $j('#refers_error').slideDown().delay(30000).slideUp();
        return false;
    }
});

$j(document).ready(function(){
    if($j('#affiliate_id').val() == '')
    {
        $j('#form-validate').find('button[type="submit"]').prop('disabled', true);
    } else {
        $j('#form-validate').find('button[type="submit"]').removeProp('disabled');
    }
});

function checkMobileNumber(e)
{
    var er = /^\d+$/;
    if (!er.test(e.val().replace(/[\(\)-\s]/g,''))) {
        if (e.val().length >= 3) {
            e.val('');
            $j('#refers_mobile_error').slideDown().delay(3000).slideUp();
        } else {
            $j('#refers_mobile_error').show();
        }
        return false;
    } else {
        $j('#refers_mobile_error').hide();
    }

    var _number = e.val().replace(/^(\d{3})(\d{3})(\d{4})+$/, "($1) $2-$3");
    if (_number.length <= 14)
        e.val(_number);
    else {
        e.val(_number.substr(0, 14));
    }
}

$j(function () {
    $j("#affiliate_name").keyup(function (e) {
        var checklength = $j('#affiliate_name').val().length;
        checkMobileNumber($j(this));
        if (checklength >= 6) {
            var projects = '';
            $j("#affiliate_name").autocomplete({
                minLength: 7,
                source: projects,
                focus: function (event, ui) {
                    $j("#affiliate_name").val(ui.item.desc);
                    return false;
                },
                select: function (event, ui) {
                    $j("#affiliate_name").val(ui.item.desc);
                    $j("#affiliate_id").val(ui.item.value);
                    $j("#affiliate_message_hidden_email").hide();
                    $j('#no_refers_me').attr('checked', false);
                    $j('#refers_mobile_error').hide();
                    $j('#refers_error').hide();
                    $j('#form-validate').find('button[type="submit"]').removeProp('disabled');
                    return false;
                }
            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                return $j( "<li>" )
                    .append( "<div>" + item.label + "<br /><b>" + item.desc + "</b></div>" )
                    .appendTo( ul );
            };
        }
    });
});

function checkAffiliateCouponCodeExisting(requestUrl, el) {
    var affiliate_name = $('affiliate_name').value;
    var affiliate_id = $('affiliate_id').value;
    var no_refers_me = $('no_refers_me').checked;
    var affiliate_id = $j('#affiliate_id').val();

    if(affiliate_id == '')
    {
        checkMobileNumber($j("#affiliate_name"));
        var params = {affiliate_name: affiliate_name, affiliate_id: affiliate_id};
        $('affiliate-please-wait-email').show();
        $('affiliate_message_hidden_email').hide();

        $j('#form-validate').find('button[type="submit"]').prop('disabled', true);
        new Ajax.Updater(
            'affiliate_message_hidden_email',
            requestUrl,
            {
                method: 'get',
                onComplete: function () {
                    endCheckEmailRegister();
                    if (affiliate_name == '' && no_refers_me === false)
                        $j('.error_refers_me').show();
                    else
                        $j('#form-validate').find('button[type="submit"]').removeProp('disabled');

                },
                onSuccess: '',
                onFailure: '',
                parameters: params,
                postBody: params
            }
        );
    }
}

function endCheckEmailRegister() {
    setTimeout(function(){
        $j('#affiliate_id').val($j('#is_valid_account_id').val());
        $j('#affiliate_name').val($j('#valid_name').val());
    },1000);
    $('affiliate-please-wait-email').hide();
    $('affiliate_message_hidden_email').show();
    if ($('is_valid_email').value == '0') {}
}

function checkNoRefer() {
    $('affiliate_name').value = '';
    $('affiliate_id').value = '';
    $j('.close-icon').hide();
    if ($('no_refers_me').checked == false) {
        $('affiliate_name').addClassName('required-entry');
    } else {
        $('affiliate_name').removeClassName('required-entry');
        $('affiliate_message_hidden_email').hide();
    }
}
*/
