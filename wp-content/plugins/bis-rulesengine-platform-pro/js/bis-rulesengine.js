/**
 * Shows error message.
 *
 * @param message
 */
function bis_showErrorMessage(message) {

    jQuery("#alert_failure_message").html(message);
    jQuery("#alert_failure_id").show();

}

/**
 * Shows success message.
 *
 * @param message
 */
function bis_showSuccessMessage(message) {

    jQuery("#alert_success_message").html(message);
    jQuery("#alert_success_id").show();
    jQuery("#alert_success_id").fadeOut( 3000   , function() {

    });

}
/**
 * Generic method for customization of message format.
 *
 * @param message
 */
function bis_alert(message) {
    BootstrapDialog.show({
        title: '<h4>Error</h4>',
        message: '<h4>' + message + '</h4>',
        type: BootstrapDialog.TYPE_DANGER,
        buttons: [{
            id: 'btn-ok',
            label: 'OK',
            cssClass: 'btn-primary',
            autospin: false,
            action: function (dialogRef) {
                dialogRef.close();
            }
        }]
    });
}

/**
 * Generic method for customization of message format.
 *
 * @param message
 */
function bis_warn(message) {
    BootstrapDialog.show({
        title: '<h4>Warning</h4>',
        message: '<h4>' + message + '</h4>',
        type: BootstrapDialog.TYPE_WARNING,
        buttons: [{
            id: 'btn-ok',
            label: 'OK',
            cssClass: 'btn-primary',
            autospin: false,
            action: function (dialogRef) {
                dialogRef.close();
            }
        }]
    });
}

/**
 * Confirm window
 *
 * @param {type} message
 * @param {type} callback
 * @returns {undefined}
 */
function bis_confirm(message, callback) {

    new BootstrapDialog({
        title: '<h4>Confirm</h4>',
        message: message,
        closable: false,
        type: BootstrapDialog.TYPE_WARNING,
        data: {
            'callback': callback
        },
        buttons: [{
            label: 'Cancel',
            action: function (dialog) {
                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                dialog.close();
            }
        }, {
            label: 'OK',
            cssClass: 'btn-primary',
            action: function (dialog) {
                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                dialog.close();
            }
        }]
    }).open();


}

/**
 * This method used to validate url.
 *
 * @param url
 * @returns {boolean}
 */
function bis_validate_url(url) {

    // Supports localhost and ip address
    var regexp = /^(http|https)?:\/\/[a-zA-Z0-9]/;

    // More strict validation for production
    //var regexp = /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/;

    if (!(url.substr(0, 7) === "http://" || (url.substr(0, 8) === "https://" ))) {
        bis_alert(breCV.protocal_missing);
        return false;
    }

    if (!regexp.test(url)) {
        bis_alert(breCV.invalid_url);
        return false;
    }

    return true;
}

/**
 * Generic method to validate rule name.
 *
 * @param ruleName
 * @returns {boolean}
 */
function bis_validateRulesName(ruleName) {

    if (ruleName === "") {
        bis_alert(breCV.enter_rule_name);
        return false;
    } else {
        if (bis_isContainsSpecialChars(ruleName)) {
            bis_alert("Rule name should not contain special characters.");
            return false;
        }
    }

    return true;
}

/**
 * Generic method to validate rule name.
 *
 * @param ruleName
 * @returns {boolean}
 */
function bis_validateName(value, messages) {

    if (value === "") {
        bis_alert(messages[0]);
        return false;
    } else {
        if (messages[1] && bis_isContainsSpecialChars(value)) {
            bis_alert(messages[1]);
            return false;
        }
    }

    return true;
}

/**
 * This method is used to return the localized values.
 *
 * @param response
 * @param brelObj
 * @returns {*}
 */
function bis_get_localized_values(response, brelObj) {

    for(i=0;i<response.length; i++) {
        response[i].name =  brelObj[response[i].id];
    }

    return response;
}

/**
 * This method is used to show the page loader icon.
 * 
 * @param {type} xhr
 * @returns {undefined}
 */
function bis_before_request_send(xhr) {
    jQuery(".page-loader").show();
    jQuery("body").addClass("loading");
}

/**
 * This method is used to hide the page loader icon.
 * 
 * @param {type} xhr
 * @returns {undefined}
 */
function bis_request_complete() {
    jQuery(".page-loader").hide();
    jQuery("body").removeClass("loading");
}

/**
 * 
 * Returns the domain name bases on the URL.
 * @param {type} url
 * @returns {extractDomain.domain}
 */
function bis_extractDomain(url) {
    var domain;
    //find & remove protocol (http, ftp, etc.) and get domain
    if (url.indexOf("://") > -1) {
        domain = url.split('/')[2];
    } else {
        domain = url.split('/')[0];
    }

    //find & remove port number
    domain = domain.split(':')[0];

    return domain;
}

function bis_capsFirstLetter(str) {
    return str.substr(0, 1).toUpperCase() + str.substr(1);
}