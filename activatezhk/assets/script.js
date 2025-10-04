function remove_licence(el) {
    let response = confirm(zhaket_guard.confirm_msg);
    if (response !== true) return;

    if (el.classList.contains('disable')) return;
    el.classList = 'disable';
    var resultDiv, licenseInput = jQuery('body').find('#code-style'),
        thisEl = jQuery('#license-message'),
        resultDiv = message_div_prepare(thisEl);

    jQuery.ajax({
        url: zhaket_guard.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: zhaket_guard.this_slug + '_request_deactivate',
            nonce:zhaket_guard.nonce,
        },
    })
        .always(function (result) {
            console.log(result);
            resultDiv.removeClass('waiting');
            el.classList = '';
        })
        .done(function (result) {
            guard_show_ajax_message(thisEl, result);
        })
        .fail(function () {
            thisEl.css('background', 'red').find('.result').html(zhaket_guard.wrong_license_message);
        });
}
function recheck_licence(el) {
    if (el.classList.contains('disable')) return;
    el.classList = 'disable';
    var resultDiv, licenseInput = jQuery('body').find('#code-style'),
        thisEl = jQuery('#license-message'),
        resultDiv = message_div_prepare(thisEl);

    jQuery.ajax({
        url: zhaket_guard.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: zhaket_guard.this_slug + '_request_recheck',
            nonce:zhaket_guard.nonce,
        },
    })
        .always(function (result) {
            console.log(result);
            resultDiv.removeClass('waiting');
            el.classList = '';
        })
        .done(function (result) {
            guard_show_ajax_message(thisEl, result,false);
        })
        .fail(function () {
            thisEl.css('background', 'red').find('.result').html(zhaket_guard.wrong_license_message);
        });
}
function install_licence(el) {
    if (el.classList.contains('disable')) return;
    el.classList = 'disable';
    var licenseInput = jQuery('body').find('#license-input'),
        license = licenseInput.val(),
        thisEl = jQuery('#license-message'),
        resultDiv = message_div_prepare(thisEl);
    if (license.length<10)
    {
        thisEl.css('background', 'red').find('.result').html(zhaket_guard.please_add_valid_license);
        licenseInput.removeAttr('disabled').focus();
        resultDiv.removeClass('waiting');
        el.classList = '';
        return;
    }
    licenseInput.attr('disabled', 'disabled');

    jQuery.ajax({
        url: zhaket_guard.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: zhaket_guard.this_slug + '_request_active',
            license: license,
            nonce:zhaket_guard.nonce,
        },
    })
        .always(function (result) {
            console.log(result);
            resultDiv.removeClass('waiting');
            el.classList = '';
            licenseInput.removeAttr('disabled').focus();
        })
        .done(function (result) {
            guard_show_ajax_message(thisEl, result);
        })
        .fail(function (result) {
            thisEl.css('background', 'red').find('.result').html(zhaket_guard.wrong_license_message);
        });
}

function guard_page_html() {
    jQuery.ajax({
        url: zhaket_guard.ajax_url,
        type: 'GET',
        dataType: 'json',
        data: {
            action: zhaket_guard.this_slug + '_guard_html',
            nonce:zhaket_guard.nonce,
        },
    }).always(function (result) {
        jQuery('#main-guard-page').html(result.responseText);
    });
}

function message_div_prepare(thisEl) {
    thisEl.html('').css('background', '#445a93');
    thisEl.slideDown(
        {
            start: function () {
                jQuery(this).css({
                    display: "flex"
                })
            }
        }
    );
    thisEl.html('<div class="result waiting"></div>');
    let result = thisEl.find('.result');
    return result;

}

function guard_show_ajax_message(thisEl, result,return_html=true) {
    if (result.message !== undefined) {
        var cl={};
        if (result.status!==undefined){
            if (result.status===false){ cl={'background':'red'};}else{
                setTimeout(function () {
                    window.location.reload();
                },3000)
            }
        }
        thisEl.css(cl).find('.result').addClass(cl).append(result.message).slideDown(150);
    } else {
        console.log(result);
        thisEl.css('background', 'red').find('.result').append(zhaket_guard.view_problem_console_log).slideDown(150);
    }

    if (result.status === true && return_html===true) {
        setTimeout(function () {
            guard_page_html();
        }, 1000);
    }
}