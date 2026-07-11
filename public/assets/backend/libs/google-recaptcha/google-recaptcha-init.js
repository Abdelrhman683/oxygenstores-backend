"use strict";

let recaptchaSiteKey = $('#get-google-recaptcha-key').data('value');
let recaptchaGenerateStatus = $('#get-google-recaptcha-status').data('value')?.toString();
recaptchaGenerateStatus = recaptchaGenerateStatus === 'true' || recaptchaGenerateStatus === '1' || recaptchaGenerateStatus === 1;

function showDefaultCaptchaSection($element, defaultSectionElement) {
    let $form = $element.closest('form');

    if ($element.next('[name="set_default_captcha"]').length === 0) {
        $element.after('<input type="hidden" name="set_default_captcha" class="set_default_captcha_value" value="1">');
    } else {
        $form.find('[name="set_default_captcha"]')?.val(1);
    }

    $form.find('.dynamic-default-and-recaptcha-section')?.addClass('active');

    if($form.find('.default-captcha-container')?.length > 0){
        let defaultCaptchaContainer = $form.find('.default-captcha-container');
        getSessionRecaptchaCode(defaultCaptchaContainer.data("session"), defaultCaptchaContainer.find("input"));
    }

    let defaultDynamicElement = $(defaultSectionElement);
    if (defaultDynamicElement?.length > 0) {
        defaultDynamicElement.find('[name="default_captcha_value"]')?.removeAttr('required');
        defaultDynamicElement.removeClass('d-none');
    }
    setTimeout(function () {
        $form.find('[type="submit"]').removeAttr('disabled');
    }, 5000);
}

function generateRecaptcha($element, $input, action, defaultSectionElement) {
    let defaultDynamicElement = $(defaultSectionElement);
    if (defaultDynamicElement?.length > 0) {
        defaultDynamicElement.find('[name="default_captcha_value"]')?.removeAttr('required');
    }
    let generatedToken = null;
    if (typeof grecaptcha !== 'undefined') {
        try {
            grecaptcha.execute(recaptchaSiteKey, { action: action })
                .then(function (token) {
                    $element.val(token);
                    generatedToken = token;
                    defaultDynamicElement?.find('[name="default_captcha_value"]')?.val('');
                    // Enable submit button after token is set
                    let $form = $element.closest('form');
                    $form.find('[name="set_default_captcha"]')?.val(0);
                    $form.find('[type="submit"]').removeAttr('disabled');
                })
                .catch(function () {
                    $element.val('');
                    showDefaultCaptchaSection($element, defaultSectionElement);
                });
        } catch (err) {
            showDefaultCaptchaSection($element, defaultSectionElement);
        }
    } else {
        showDefaultCaptchaSection($element, defaultSectionElement);
    }
}

$('.render-grecaptcha-response').each(function () {
    let $element = $(this);
    let action = $element.data('action');
    let defaultSectionElement = $element.data('default-captcha');
    let $input = $element.next('.set_default_captcha_value');

    if ($input.length === 0) {
        $element.after('<input type="hidden" name="set_default_captcha" class="set_default_captcha_value" value="">');
        $input = $element.next('.set_default_captcha_value');
    }

    let formElement = $element.closest('form');
    if (!formElement.length) {
        console.warn('No form found for element:', $element);
        return;
    }

    let submitButton = formElement.find('[type="submit"]');

    submitButton.on('mouseover mousedown', function () {
        if (!$input.val()) {
            submitButton.attr('disabled', true);
            generateRecaptcha($element, $input, action, defaultSectionElement);
            setTimeout(function () {
                submitButton.attr('disabled', false);
            }, 2000);
        }
    });

    formElement.on('mouseover mousedown', function () {
        if (!$input.val()) {
            submitButton.attr('disabled', true);
            generateRecaptcha($element, $input, action, defaultSectionElement);
            setTimeout(function () {
                submitButton.attr('disabled', false);
            }, 2000);
        }
    });

    submitButton.on('click', function () {
        setTimeout(function () {
            generateRecaptcha($element, $input, action, defaultSectionElement);
        }, 10000);
    });

    // Listen for any typing/select/file change inside the form
    formElement.on('input change', 'input, textarea, select', function () {
        if (!$input.val()) {
            submitButton.attr('disabled', true);
            generateRecaptcha($element, $input, action, defaultSectionElement);
            submitButton.attr('disabled', false);
        }
    });
});


$('.default-captcha-container').each(function () {
    $(this).hide();
});

function getSessionRecaptchaCode(sessionKey, inputSelector) {
    try {
        let routeGetSessionRecaptchaCode = $(
            "#route-get-session-recaptcha-code"
        );
        let csrfToken = $('meta[name="_token"]').attr("content");
        if (routeGetSessionRecaptchaCode.data("mode").toString() === "dev") {
            let string = ".";
            let intervalId = setInterval(() => {
                if (string === "......") {
                    string = ".";
                }
                string = string + ".";
                $(inputSelector).val(string);
            }, 100);

            setTimeout(() => {
                clearInterval(intervalId);
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                });
                $.ajax({
                    type: "POST",
                    url: $("#route-get-session-recaptcha-code").data("route"),
                    data: {
                        _token: csrfToken,
                        sessionKey: sessionKey,
                    },
                    success: function (response) {
                        $(inputSelector).val(response?.code);
                    },
                });
            }, 1000);
        }
    } catch (e) {
        console.log(e);
    }
}
